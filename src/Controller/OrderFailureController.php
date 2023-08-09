<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;

class OrderFailureController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager=$entityManager;
    }
    #[Route('/commande/echec/{stripeSessionId}', name: 'app_order_failure')]
    public function index($stripeSessionId): Response
    {

          $order =  $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        //  dd($order);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('/');
        }



        return $this->render('order_failure/orderFailure.html.twig', [
            'order' => $order,
        ]);
    }
}
