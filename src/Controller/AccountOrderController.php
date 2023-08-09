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
         $orders=  $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser()); 

      


        return $this->render('account/order.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/compte/mes-commandes{reference}', name: 'app_account_one_order')]
    public function oneOrder($reference): Response
    {
         $order=  $this->entityManager->getRepository(Order::class)->findOneBy([
            'reference'=>$reference
         ]); 

         if(!$order || $order->getUser() !=$this->getUser()) {
            return $this->redirectToRoute('app_account_order');
         }
      


        return $this->render('account/one_order.html.twig', [
            'order' => $order,
        ]);
    }
}
