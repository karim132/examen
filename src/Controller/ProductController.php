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
        Request $request,EntityManagerInterface $entityManager,
        PaginatorInterface $paginator
    ): Response
    {
        //Création d'une nouvelle recherche
        $search =new Search;
        //création d'un nouveau formulaire
        $form= $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);
      
        // Requête pour obtenir les produits
        $query = $entityManager->getRepository(Product::class)->findWithSearch($search);

        //Pagination des résultats
        $pagination= $paginator->paginate(
            $query, // requête à paginer
            $request->query->getInt('page',1), //Numéro de page par défaut
            9 // Nombre déléments par page
        );
        return $this->render('product/index.html.twig', [
            'products' => $pagination,
            'form' => $form->createView()        
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
            ),
          ]);
}
}
