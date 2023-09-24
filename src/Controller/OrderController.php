<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
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
        //si user n'a pas encore d'adresse enregistreé 
       if(!$this->getUser()->getAddresses()->getValues())
        {
            //redirige vers formulaire d'ajout d'adresse
            return $this->redirectToRoute('app_address_add');
        }
        //création du formulaire OrderType (null car le formulaire n'est pas lié à une entité et 'user' pour récupérer uniquement mes adresses)
        $form = $this->createForm(OrderType::class, null, [
            'user'=> $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getTotal()
        ]);
    }

    #[Route('/commande/recap', name: 'app_order_recap' , methods:['POST'])]
    public function add(Request $request,Cart $cart): Response
    {
        //création du formulaire OrderType (null car le formulaire n'est pas lié à une entité et 'user' pour récupérer uniquement mes adresses)
        $form = $this->createForm(OrderType::class, null, [
            'user'=> $this->getUser()
        ]);
         $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

          // récupération des variables avant enregistrement en allant les chercher avec des 'get' 
             
           $date = new \DateTime();
           $reference = $date->format('dmY').'-'.uniqid();
           $carriers = $form->get('carriers')->getData();
           $delivery = $form->get('addresses')->getData();
           $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
           $delivery_content .= '<br/>'.$delivery->getPhone();
           //si société renseignée dans le formulaire
           if($delivery->getCompany()){
           $delivery_content .= '<br/>'.$delivery->getCompany();
           }

           $delivery_content .= '<br/>'.$delivery->getAdress();
           $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
           $delivery_content .= '<br/>'.$delivery->getCountry();
           
            //enregistre ma commande Order()
           //instanciation de ma classe Order()
           $order = new Order();
          //set chaque propriété de mon entité OrderDetails
           
           $order->setUser($this->getUser());
           $order->setReference($reference);
          
           $order->setCreatedAt($date);
           $order->setCarrierName($carriers->getName());
           $order->setCarrierPrice($carriers->getPrice()); 
           $order->setDelivery($delivery_content);
           $order->setStatus(0);

            //fige les données entrées dans le formulaire
           $this->entityManager->persist($order);

          // enregistrer mes produits OderDetails()
           //boucle pour récupérer chaque produit du panier
           foreach($cart->getTotal() as $product){
            //instanciation de ma classe Order()
           $orderDetails = new OrderDetails;
           //set chaque propriété de mon entité OrderDetails
           $orderDetails->setMyOrder($order);
           $orderDetails->setProduct($product['product']->getName());
           $orderDetails->setQuantity($product['quantity']);
           $orderDetails->setPrice($product['product']->getPrice());
           $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);

          //fige les données entrées dans le formulaire
           $this->entityManager->persist($orderDetails);
           }
           //enregistre $order et $orderDetails en BDD
           $this->entityManager->flush();

           //le render ne s'affiche qu'avec les conditions du if
           return $this->render('order/add.html.twig', [
            'cart'=> $cart->getTotal(),
            'carrier'=> $carriers,
            'delivery'=> $delivery_content,
            'reference'=> $order->getReference()
            
        ]);
     }
        //si pas de post du formulaire, redirige vers le panier
        return $this->redirectToRoute('cart_index');
          
        }

      

}
