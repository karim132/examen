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
          //récupère le stripesessionid en cas de 'cancel_url'
          $order =  $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

          //si pas de $order ou si l'utilisateur associé à $order est différent de l'utilisateur connecté
        if(!$order || $order->getUser() != $this->getUser()){
           //redirige vers page d'accueil
            return $this->redirectToRoute('/');
        }
        return $this->render('order_failure/order_failure.html.twig', [
            'order' => $order,
        ]);



    }
}
