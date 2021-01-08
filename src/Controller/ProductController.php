<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function productPage(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([], [], 3);

        return $this->render('product/product.html.twig', [
            'products' => $products
        ]);
    }
}
