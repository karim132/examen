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
         //initialisation de l'url
        $YOURDOMAIN= 'http://127.0.0.1:8000';
        //initialisation d'un tableau vide
        $productStripe =[];
        //installation de la clé api
        Stripe::setApiKey(apiKey:'sk_test_51MyWHoKxjzuU1nkttAKhcEcfV3zecarWMYNquuWXijAHjwYPBKPXvghTzm2Ri7oezArOaxzqBXC2rW1XowOy8qvy00sj9BBmDC');

    //récupère la commande par sa référence
   $order= $this->entityManager->getRepository(Order::class)->findOneBy([
     'reference'=> $reference
   ]);

   // si pas de commande
     if(!$order){
      //redirige vers ('cart_index')
      return $this->redirectToRoute('cart_index');
   }
   //boucle pour récupérer chaque produit de la commande
   foreach ($order->getOrderDetails()->getValues() as $product){

        // récupère les produits par le nom
        $productData= $this->entityManager->getRepository(Product::class)->findOneBy([
        'name'=> $product->getProduct()
     ]);
     // infos demandées par stripe dans leur doc pour les produits
     $productStripe[] = [
      'price_data'=> [
            //devise
           'currency'=>'eur',
           //prix unitaire
           'unit_amount'=> $productData->getPrice(),
           //le nom de mon produit
           'product_data'=>[
           'name' => $product->getProduct(),
         ]
        ],
           'quantity'=>$product->getQuantity(),
     ];
      // dd($productStripe);
   }
   // infos demandées par stripe dans leur doc pour la livraison
   $productStripe[] = [
     'price_data'=> [
         'currency'=>'eur',
         'unit_amount'=>$order->getCarrierPrice(),
         'product_data'=>[
             'name' => $order->getCarrierName(),
         ]
        ],
           'quantity'=> 1,
  ];
   //création de ma checkout session
   $checkout_session = Session::create([
    // je donne à stripe l'email du client pour affichage
     'customer_email'=> $this->getUser()->getUserIdentifier(),
    //quel type de paiement je souhaite
    'payment_method_types' => ['card'],
    //je passe ma variable $productStripe avec toutes les infos rentrés ci-dessus  à la checkout session
    'line_items' => [[
        $productStripe
    ]],
    'mode' => 'payment',
             //route en cas de succès + recupération du CHECKOUT_SESSION_ID
            'success_url' => $YOURDOMAIN.'/commande/success/{CHECKOUT_SESSION_ID}',
            //route en cas d'échec
            'cancel_url' => $YOURDOMAIN.'/commande/echec/{CHECKOUT_SESSION_ID}'
         ]);
        
         //set le StripeSessionId
        $order->setStripeSessionId($checkout_session->id);
        
         //enregistre en bdd
         $this->entityManager->flush();

  return new RedirectResponse($checkout_session->url);

  }


}












