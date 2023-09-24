<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AccountOrderController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
       $this->entityManager=$entityManager;
    }

    #[Route('/compte/mes-commandes', name: 'app_account_order')]
    public function index(): Response
    {
        //Requête personnalisée pour récupérer les commandes payées en BDD.
         $orders=  $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser()); 
    
        return $this->render('account/order.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/compte/mes-commandes{reference}', name: 'app_account_one_order')]
    public function oneOrder($reference): Response
    {
        //récupère une commande en bdd par sa référence pour affichage de celle-ci
         $order=  $this->entityManager->getRepository(Order::class)->findOneBy([
            'reference'=>$reference
         ]); 
        //si pas de $order ou si l'utilisateur associé à $order est différent de l'utilisateur connecté
         if(!$order || $order->getUser() !=$this->getUser()) {
            //redirige vers la route app_account_order
            return $this->redirectToRoute('app_account_order');
         }   
        return $this->render('account/one_order.html.twig', [
            'order' => $order,
        ]);
    }
}
