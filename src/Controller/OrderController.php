<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Carrier;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Stripe\Stripe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class OrderController extends AbstractController
{
private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager=$entityManager;
    }


    #[Route('/commande', name: 'app_order')]
    public function index(Cart $cart): Response
    {
       if(!$this->getUser()->getAddresses()->getValues())
        {
            return $this->redirectToRoute('app_address_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user'=> $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart'=> $cart->getTotal()
            
            
        ]);
    }

    #[Route('/commande/recap', name: 'app_order_recap' , methods:['POST'])]
    public function add(Request $request,Cart $cart): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user'=> $this->getUser()
        ]);


         $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){


            //    dd($form->getData());
           //enregistrer ma commande Order()
           $date = new \DateTime();
           $carriers =$form->get('carriers')->getData();
           $delivery =$form->get('addresses')->getData();
           $delivery_content= $delivery->getFirstname().' '.$delivery->getLastname();
           $delivery_content .= '<br/>'.$delivery->getPhone();

           if($delivery->getCompany()){
           $delivery_content .= '<br/>'.$delivery->getCompany();
           }

           $delivery_content .= '<br/>'.$delivery->getAdress();
           $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
           $delivery_content .= '<br/>'.$delivery->getCountry();
           

           $order = new Order();
           $reference = $date->format('dmY').'-'.uniqid();
           $order->setUser($this->getUser());
           $order->setReference($reference);
          
           $order->setCreatedAt($date);
           $order->setCarrierName($carriers->getName());
           $order->setCarrierPrice($carriers->getPrice()); 
           $order->setDelivery($delivery_content);
           $order->setStatus(0);
            // dd($order);

           $this->entityManager->persist($order);

          

           foreach($cart->getTotal() as $product){
           $orderDetails = new OrderDetails;
           $orderDetails->setMyOrder($order);
           $orderDetails->setProduct($product['product']->getName());
           $orderDetails->setQuantity($product['quantity']);
           $orderDetails->setPrice($product['product']->getPrice());
           $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
           $this->entityManager->persist($orderDetails);



           
            // dd($product);
           }
            //  dd($products_for_stripe);

         $this->entityManager->flush();

          


        //    dump($checkout_session->id);
        //    dd($checkout_session);

           // enregistrer mes produits
           return $this->render('order/add.html.twig', [
            // 'form' => $form->createView(),
        //  'stripe_key' => $_ENV["STRIPE_KEY"],
            'cart'=> $cart->getTotal(),
            'carrier'=> $carriers,
            'delivery'=> $delivery_content,
            'reference'=> $order->getReference()
            
        ]);
     }

        return $this->redirectToRoute('cart_index');
          
        }

      

}
