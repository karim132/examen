<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/produits', name: 'app_product')]
    public function index(
        ProductRepository $productsRepository,
        Request $request,EntityManagerInterface $entityManager,
        PaginatorInterface $paginator
    ): Response
    {
        //  $pagination= $productsRepository->findAllWithData($request->query->getInt('page',1));


         
        
        $search =new Search;
        $form= $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);
        
        //  $products= $productsRepository->findAll();
        $query = $entityManager->getRepository(Product::class)->findWithSearch($search);
        $pagination= $paginator->paginate(
            $query,
            $request->query->getInt('page',1),
            9
        );

        // if ($form->isSubmitted() && $form->isValid()){

        // }

        // $pagination= $productsRepository->findAllWithData($request->query->getInt('page',1));

        return $this->render('product/index.html.twig', [
            // 'pagination'=> $productsRepository->findAllWithData($request->query->getInt('page',1)),
            'products' => $pagination,
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
