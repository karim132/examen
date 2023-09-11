<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/produits', name: 'app_product')]
    public function index(ProductRepository $productsRepository,Request $request,EntityManagerInterface $entityManager ): Response
    {
        $products= $productsRepository->findAll();

        $search =new Search;
        $form= $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            
            $products = $entityManager->getRepository(Product::class)->findWithSearch($search);
            //  dd($search);
        }
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
            //  dd($products)
        ]);
   
    }

    // Route qui affiche un produit en particulier
    #[Route('/produits/{id}', name: 'app_oneProduct', methods: ['GET', 'POST'])]
    public function show($id, ProductRepository $oneProductRepository): Response
    {
        
        // Affiche le produit demandé dans le template dédié
        return $this->render('product/oneProduct.html.twig', [
            // Récupère le produit demandé par son id
            'oneProduct' => $oneProductRepository->findOneBy(
                ['id' => $id],
                    // dd($oneProduct)
            ),
          ]);
}
}
