<?php

 namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Classe\Cart;


 class CartController extends AbstractController
 {
#[Route("/panier/add/{id}", name: 'cart_add')]
public function add(Cart $cart,$id) : Response{
    //ajoute le produit au panier par son id
    $cart->add($id);
    //redirige vers le panier
    return $this->redirectToRoute('cart_index');
}

    #[Route("/panier/ajout/{id}", name: 'cart_ajout')]
    public function ajout(Cart $cart,$id) : Response{
        //ajoute le produit au panier par son id
        $cart->add($id);
        //redirige vers le panier
        return $this->redirectToRoute('app_product');
    }

#[Route("/panier/remove/{id}", name: 'cart_remove')]
public function remove(Cart $cart,$id) : Response{
   //décrémente le produit au panier par son id
    $cart->remove($id);
   //redirige vers le panier
    return $this->redirectToRoute('cart_index');
}


#[Route("/panier/delete/{id}", name: 'cart_delete')]
public function delete(Cart $cart,$id) : Response{
    // supprime le produit du panier par son id  
    $cart->delete($id);

    return $this->redirectToRoute('cart_index');
}

#[Route("/panier/empty", name: 'cart_empty')]
public function empty(Cart $cart) : Response{
    // supprime tous les produits du panier
    $cart->removeAll();
    //redirige vers le panier
    return $this->redirectToRoute('cart_index');
}

#[Route("/panier", name: 'cart_index')]
public function index(Cart $cart) : Response{
    // récupère la totalité du panier
     $cart->getTotal();
    
    return $this->render('cart/index.html.twig',[
        'cart'=>$cart->getTotal()
    ]);
}
}

