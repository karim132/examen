<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Classe\Cart;


class StripeController extends AbstractController
{

    private $entityManager; 
    private $generator;

     public function __construct(EntityManagerInterface $entityManager , UrlGeneratorInterface $generator )
     {
        $this->entityManager=$entityManager;
        $this->generator= $generator;
     }

   
#[Route('/order/create-session-stripe/{reference}', name: 'app_stripe')]
    public function index($reference): RedirectResponse
    {
        $productStripe =[];
        $YOURDOMAIN= 'http://127.0.0.1:8000';


        Stripe::setApiKey(apiKey:'sk_test_51MyWHoKxjzuU1nkttAKhcEcfV3zecarWMYNquuWXijAHjwYPBKPXvghTzm2Ri7oezArOaxzqBXC2rW1XowOy8qvy00sj9BBmDC');
    

//     $productStripe[] = [];

   $order= $this->entityManager->getRepository(Order::class)->findOneBy([
     'reference'=> $reference
   ]);

  //  dd($order);

     if(!$order){
      return $this->redirectToRoute('cart_index');
   }

 

   foreach ($order->getOrderDetails()->getValues() as $product){
        $productData= $this->entityManager->getRepository(Product::class)->findOneBy([
        'name'=> $product->getProduct()
     ]);
     $productStripe[] = [
        'price_data'=> [
                        'currency'=>'eur',
                        'unit_amount'=> $productData->getPrice(),
                        'product_data'=>[
                            'name' => $product->getProduct()
                        ]
                         ],
                        'quantity'=>$product->getQuantity(),
     ];
    
   }
    // dd($productStripe);

   $productStripe[] = [
     'price_data'=> [
         'currency'=>'eur',
         'unit_amount'=>$order->getCarrierPrice(),
         'product_data'=>[
             'name' => $order->getCarrierName()
         ]
        ],
           'quantity'=> 1,
  ];
//   dd($productStripe);

   $checkout_session = Session::create([
    'customer_email'=> $this->getUser()->getUserIdentifier(),
    'payment_method_types' => ['card'],
    'line_items' => [[
        $productStripe
    ]],
    'mode' => 'payment',
          //  'success_url' => $this->generator->generate('app_success' ,[
          //    'reference'=> $order->getReference()
          //   ],UrlGeneratorInterface::ABSOLUTE_URL) ,
          //   'cancel_url' =>  $this->generator->generate('app_error' ,[
          //   'reference'=> $order->getReference()
          //   ],UrlGeneratorInterface::ABSOLUTE_URL)
            'success_url' => $YOURDOMAIN.'/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOURDOMAIN.'/commande/echec/{CHECKOUT_SESSION_ID}'
         ]);

        // $order->setIsPaid(true);
        $order->setStripeSessionId($checkout_session->id);
        
         $this->entityManager->flush();

  return new RedirectResponse($checkout_session->url);

    }
 #[Route('/order/success/{reference}', name: 'app_success')]

 public function stripeSuccess($reference , Cart $cart) : Response
 {
     return $this->render('order/success.html.twig');
 }


 #[Route('/order/error/{reference}', name: 'app_error')]

 public function stripeError($reference , Cart $cart) : Response
 {
     return $this->render('order/error.html.twig');
 }

}












