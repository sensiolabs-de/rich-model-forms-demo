<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Price;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render('dashboard.html.twig', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
