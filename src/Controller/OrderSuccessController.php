<?php

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;

class OrderSuccessController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager=$entityManager;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_success')]
    public function index($stripeSessionId, Cart $cart): Response
    {


        $order =  $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        //  dd($order);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('/');
        }

        if(!$order->isIsPaid()){
           $cart->removeAll();
            
        $order->setIsPaid(true);
        $this->entityManager->flush();

        }

        return $this->render('order_success/orderSuccess.html.twig', [
            'order' => $order,
        ]);
    }
}
