<?php

// namespace App\Controller;

// use App\Classe\Cart;
// use App\Entity\Order;
// use App\Entity\Product;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\RedirectResponse;
// use Symfony\Component\Routing\Annotation\Route;
// use Stripe\Stripe;
// use Doctrine\ORM\EntityManagerInterface;
// use Stripe\Checkout\Session;
// use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

// class PaymentController extends AbstractController
//  {

//     private $entityManager; 
//     private $generator;

//     public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $generator)
//     {
//         $this->entityManager=$entityManager;
//         $this->generator= $generator;
//     }

// #[Route('/order/create-session-stripe/{reference}', name: 'app_stripe')]
// public function stripeCheckout($reference) : RedirectResponse 
// {

  
//     // Stripe::setApiKey(apiKey:'sk_test_51MyWHoKxjzuU1nkttAKhcEcfV3zecarWMYNquuWXijAHjwYPBKPXvghTzm2Ri7oezArOaxzqBXC2rW1XowOy8qvy00sj9BBmDC');
    

//     $productStripe[] = [];

//   $order= $this->entityManager->getRepository(Order::class)->findOneBy([
//     'reference'=> $reference
//   ]);

//    if(!$order){
//       return $this->redirectToRoute('cart_index');
//    }
  
//   foreach ($order->getOrderDetails()->getValues() as $product){
//     $productData= $this->entityManager->getRepository(Product::class)->findOneBy([
//         'name'=> $product->getProduct()
//     ]);
//     $productStripe[]=[
//         'price_data'=> [
//             'currency'=>'eur',
//             'unit_amount'=> $productData->getPrice(),
//             'product_data'=>[
//                 'name' => $product->getProduct()
//             ]
//             ],
//                'quantity'=>$product->getQuantity(),
//     ];
//   }

//   $productStripe[] = [
//     'price_data'=> [
//         'currency'=>'eur',
//         'unit_amount'=>$order->getCarrierPrice(),
//         // "source" => $request->request->get('stripeToken'),
//         'product_data'=>[
//             'name' => $order->getCarrierName()
//         ]
//         ],
//            'quantity'=> 1,
//   ];

//  Stripe::setApiKey($_ENV["STRIPE_KEY"]);

// $checkout_session = Session::create([
//     'customer_email'=> $this->getUser()->getUserIdentifier(),
//     'payment_method_types' => ['cart'],
//     'line_items' => [[
//    $productStripe
//   ]],
// 'mode' => 'payment',
//    'success_url' => $this->generator->generate('app_success' ,[
//     'reference'=> $order->getReference()
//    ],UrlGeneratorInterface::ABSOLUTE_URL) ,
//    'cancel_url' =>  $this->generator->generate('app_error' ,[
//     'reference'=> $order->getReference()
//    ],UrlGeneratorInterface::ABSOLUTE_URL)
// ]);

// // $order->setStripeSessionId($checkout_session->id);

// // $this->entityManager->flush;
// return $this->redirectToRoute('stripe', [], Response::HTTP_SEE_OTHER);
//  return new RedirectResponse($checkout_session->url);
 

// }

// #[Route('/order/success/{reference}', name: 'app_success')]

// public function stripeSuccess($reference , Cart $cart) : Response
// {
//     return $this->render('order/success.html.twig');
// }


// #[Route('/order/error/{reference}', name: 'app_error')]

// public function stripeError($reference , Cart $cart) : Response
// {
//     return $this->render('order/error.html.twig');
// }




// #[Route('/order/create-session/', name: 'stripe_create_session',methods:['POST'])]

//     public function index(Cart $cart)
//     {

//         $products_for_stripe = [];
//         $YOUR_DOMAIN = 'http://127.0.0.1:8000';

//         foreach($cart->getTotal() as $product){

//             $products_for_stripe []= [
            
//                 'price_data'=> [
//                     'currency'=>'eur',
//                     'unit_amount'=>$product['product']->getPrice(),
//                     'product_data'=>[
//                         'name' => $product['product']->getName(),
//                         //  'images'=> [$YOUR_DOMAIN."/uploads/".$product['product']->getIllustration()],
//                     ],
//                     ],
//                        'quantity'=> $product['quantity'],
//                ];
               
//         }
//         dd($products_for_stripe);
//         Stripe::setApiKey(apiKey:'sk_test_51MyWHoKxjzuU1nkttAKhcEcfV3zecarWMYNquuWXijAHjwYPBKPXvghTzm2Ri7oezArOaxzqBXC2rW1XowOy8qvy00sj9BBmDC');

        

//          $checkout_session = Session::create([
//          'payment_method_types'=>['card'],
//           'line_items' => [
//              $products_for_stripe
//           ],
//           'mode' => 'payment',
//           'success_url' => $YOUR_DOMAIN . '/success.html',
//           'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
//         ]);

//         // return new RedirectResponse($checkout_session->url);
//         $response = new JsonResponse(['id'=>$checkout_session->id]);
//         return $response;
//     }
//  }