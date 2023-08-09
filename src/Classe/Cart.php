<?php

namespace App\Classe;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Product;

 class Cart

 {

   
    private $requestStack;
    private  $entityManager;
     public function __construct(RequestStack $requestStack,EntityManagerInterface $entityManager)
 {
     // ...
     $this->requestStack = $requestStack;
     $this->entityManager =  $entityManager;
    
 }

     public function add($id):void
    {
        $cart = $this->requestStack->getSession()->get('cart',[]);
        $this->getSession()->set('cart',$cart);

        // dd($cart);

        if(!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id]=1;
        }

        // dd($cart);
         $this->getSession()->set('cart', $cart);
    }

    public function remove($id)
       {

        $cart = $this->requestStack->getSession()->get('cart',[]);
        $this->getSession()->set('cart',$cart);
        

    
       
            
         //on retire le produit du panier s'il n'y a qu'un exemplaire
         //sinon on décrémente sa quantité
    
            if(!empty($cart[$id])){
                if($cart[$id] > 1){
                $cart[$id]--;
            }else{
                unset($cart[$id]);
            }
        }
        $this->getSession()->set('cart', $cart);
            

       }


    public function delete($id){

        $cart = $this->requestStack->getSession()->get('cart',[]);
        unset($cart[$id]);
        return $this->getSession()->set('cart', $cart);
    }





    public function removeAll(){
        return $this->getSession()->remove('cart');
    }


public function getTotal():array

{
    $cart=$this->getSession()->get('cart');
    $cartData = [];
    
    if($cart){
    foreach($cart as $id=>$quantity){
        $product= $this->entityManager->getRepository(Product::class)->FindOneBy(['id'=> $id]);
        if(!$product){
            //supprimer le produit puis continuer en sortant de la boucle
              $this->remove($id);
              continue;
        }
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


    // public function remove()
    // {
    //     return $this->requestStack->getSession()->remove('cart');
    // }
}