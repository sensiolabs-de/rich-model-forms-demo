<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\Product as ProductEntity;
use App\Form\Dto\Product as ProductDto;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product/", name="product_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("{id?}", name="form", methods={"POST", "GET"})
     */
    public function form(Request $request, ProductEntity $product = null): Response
    {
        $form = $this->createForm(ProductType::class, ProductDto::fromEntity($product));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ProductDto $dto */
            $dto = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dto->toEntity($product));
            $entityManager->flush();

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/remove", name="remove", methods={"GET"})
     */
    public function remove(ProductEntity $category): RedirectResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'Successfully removed');

        return $this->redirectToRoute('dashboard');
    }
}
