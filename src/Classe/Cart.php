<?php

namespace App\Classe;

// use Symfony\Component\HttpFoundation\RequestStack;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpFoundation\Session\SessionInterface;

 class Cart

 {

   
//     private $requestStack;
//     public function __construct(RequestStack $requestStack )
// {
//     // ...
//     $this->requestStack = $requestStack;
    
 }

//     public function add($id ,SessionInterface $session)
//     {
//         $cart = $this->requestStack->getSession()->get('cart');

//         // dd($cart);

//         if(!empty($cart[$id])){
//             $cart[$id]++;
//         }else{
//             $cart[$id]=1;
//         }

//         dd($cart);
//         $session->set('cart', $cart);
//     }

//     // public function get()
//     // {
//     //     return $this->requestStack->getSession()->get('cart');
//     // }

//     public function remove()
//     {
//         return $this->requestStack->getSession()->remove('cart');
//     }
// }