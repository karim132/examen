<?php

namespace App\Classe;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Product;

 class Cart

 {  
    private $requestStack;
    private  $entityManager;
     public function __construct(RequestStack $requestStack,EntityManagerInterface $entityManager)
 {
     $this->requestStack = $requestStack;
     $this->entityManager =  $entityManager;
    
 }

 public function get()
 {
     //retourne le contenu du panier
     return $this->getSession()->get('cart');
 }

     public function add($id):void
    {
        //recupère le panier dans la variable $cart
        $cart = $this->requestStack->getSession()->get('cart',[]);
        $this->getSession()->set('cart',$cart); 
        //si dans le panier il existe 1 article avec l'id du produit concerné
        if(!empty($cart[$id])){
           // incrémentation 
            $cart[$id]++;
        }else{
            //sinon si panier vide $cart =1
            $cart[$id]=1;
        }
         // set mon panier en ajoutant le produit
         $this->getSession()->set('cart', $cart);
    }

    public function remove($id)
       {
        //recupère le panier dans la variable $cart 
        $cart = $this->requestStack->getSession()->get('cart',[]);
        $this->getSession()->set('cart',$cart); 
                  
         //on retire le produit du panier s'il n'y a qu'un exemplaire
         //sinon on décrémente sa quantité

     //si dans le panier il existe 1 article avec l'id du produit concerné
            if(!empty($cart[$id])){
             //si quantité de l'article supérieur à 1
                if($cart[$id] > 1){
                //décrémentation
                $cart[$id]--;
            }else{
                //sinon retire l article du panier
                unset($cart[$id]);
            }
        }
        // set mon panier en décrémentant le produit
        $this->getSession()->set('cart', $cart);
            
       }


    public function delete($id){
        //recupère le panier dans la variable $cart 
        $cart = $this->requestStack->getSession()->get('cart',[]);
        // retire le produit du panier quelle que soit sa quantité
        unset($cart[$id]);
        // set mon panier en retirant le produit
        return $this->getSession()->set('cart', $cart);
    }



    public function removeAll(){
        //vide le panier
        return $this->getSession()->remove('cart');
    }


public function getTotal():array

{   
    //recupère le panier dans la variable $cart 
    $cart=$this->getSession()->get('cart');
    // initialisation du panier dans un tableau vide 
    $cartData = [];
    
    // si panier
    if($cart){
    //pour chaque itération de $cart avec $id en clé et $quantity en valeur
    foreach($cart as $id=>$quantity){
        // Recupère l'id du produit en bdd
        $product= $this->entityManager->getRepository(Product::class)->FindOneBy(['id'=> $id]);
        //si pas de produit
        if(!$product){
            //supprimer le produit puis continuer en sortant de la boucle
              $this->delete($id);
              continue;
        }
        // récupération dans le tableau des données récupérées suite au findOneBy
        $cartData[] = [
            'product'=> $product,
            'quantity'=> $quantity
        ];     
    }
}
    return $cartData;
}



private function getSession():SessionInterface
{
    return $this->requestStack->getSession();
}

}