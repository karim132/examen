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

    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_success')]
    public function index($stripeSessionId, Cart $cart): Response
    {


        $order =  $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        //  dd($order);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('/');
        }

        if(!$order->getStatus()){
           $cart->removeAll();
            
        $order->setStatus(true);
        $this->entityManager->flush();

        
        $mail = new Mail();
        // $content ="Bienvenue ".$user->getFirstname()."<br/>Vous pouvez dès à présent visiter notre site";
        $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),subject:'Merci pour votre commande',
        content:"Bonjour ".$order->getUser()->getFirstname()."<br/>Votre commande a bien été validée");

        }

        return $this->render('order_success/orderSuccess.html.twig', [
            'order' => $order,
        ]);
    }
}
