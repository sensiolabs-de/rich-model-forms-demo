<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\Category as CategoryEntity;
use App\Entity\Product;
use App\Form\CategoryType;
use App\Form\Dto\Category as CategoryDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category/", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("{id?}", name="form", methods={"POST", "GET"})
     */
    public function form(Request $request, CategoryEntity $category = null): Response
    {
        $form = $this->createForm(CategoryType::class, CategoryDto::fromEntity($category));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CategoryDto $dto */
            $dto = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dto->toEntity($category));
            $entityManager->flush();

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("{id}/remove", name="remove", methods={"GET"})
     */
    public function remove(CategoryEntity $category): RedirectResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $children = $entityManager->getRepository(CategoryEntity::class)->findByParent($category);

        if (count($children) !== 0) {
            $this->addFlash('danger', 'Cannot remove due to parent child relation');

            return $this->redirectToRoute('dashboard');
        }

        $products = $entityManager->getRepository(Product::class)->findByCategory($category);

        if (count($products) !== 0) {
            $this->addFlash('danger', 'Cannot remove due to product relation');

            return $this->redirectToRoute('dashboard');
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'Successfully removed');

        return $this->redirectToRoute('dashboard');
    }
}
