<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
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

    #[Route('/commande/success/{stripeSessionId}', name: 'app_order_success')]
    public function index($stripeSessionId, Cart $cart): Response
    {
      //récupère le stripesessionid en cas de 'success_url'
    $order =  $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

    //si pas de $order ou si l'utilisateur associé à $order est différent de l'utilisateur connecté
        if(!$order || $order->getUser() != $this->getUser()){
            //redirige vers page d'accueil
            return $this->redirectToRoute('/');
        }

        // si statut à 0
        if(!$order->getStatus()){
        // passage du statut de la commande à true
        $order->setStatus(true);
        //vidage du panier
        $cart->removeAll();
        //enregistre en BDD
        $this->entityManager->flush();
        
        //création d'un nouveau mail
        $mail = new Mail();

        $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),subject:'Merci pour votre commande',
        content:"Bonjour ".$order->getUser()->getFirstname().
        "<br/> Nous vous remercions pour votre commande numero <strong>".$order->getReference()."</strong>");

        }
        return $this->render('order_success/order_success.html.twig', [
            'order' => $order,
        ]);
    }
}
