<?php

 namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Classe\Cart;


//1ère méthode

// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


 class CartController extends AbstractController
 {
//     #[Route("/panier", name: 'app_cart')]
//     public function index(Request $request,SessionInterface $sessionInterface) : Response
//     {
//         $session = $request->getSession();
//         //   dd($cart->get());
//         return $this->render('cart/index.html.twig');
//     }

//     #[Route("/cart/add/{id}", name: 'app_add_to_cart')]
//     public function add($id ,Cart $cart ,SessionInterface $session) 
//        {
//         //   $cart =$session->get("cart");
//        $cart->add($id,$session);
//         return $this->redirectToRoute('cart');
//     }

    // #[Route("/cart/remove/", name: 'app_remove_my_cart')]
    // public function remove(Cart $cart)
    // {

    //     $cart->remove();
    //     return $this->redirectToRoute('produits');
    // }
// }

// 2ème méthode

// #[Route("/cart", name: 'cart_')]
// Class CartController extends AbstractController

// {
//    #[Route("/", name: 'index')]
//    public function index( SessionInterface $session,ProductRepository $productsRepository)
//    {
//        $panier =$session->get('panier',[]);

//        //on initialise des variables
//        $data = [];
//        $total= 0;
//     //    $session->set('panier',[]);

//        foreach($panier as $id => $quantity){
//         $product= $productsRepository->find($id);

//         $data[]= [
//             'product'=> $product,
//             'quantity'=> $quantity
//         ];
//         $total += $product->getPrice() * $quantity;

//        }
//     return $this->render('cart/cart.html.twig', compact('data','total'));
//    }

//     #[Route("add/{id}", name: 'app_add')]
//    public function add(Product $product, SessionInterface $session)
//    {
//     //on récupère l'id du produit
//     $id= $product->getId();

//     //on récupère le panier existant
//     $panier= $session->get('panier',[]);
        
//      //on ajoute le produit dans le panier s'il n'y est pas encore
//      //sinon on incrémente sa quantité

//         if(!empty($panier[$id])){
//             $panier[$id]++;
//         }else{
//             $panier[$id]=1;
//         }
        
//         $session->set('panier',$panier);
        
//         //on redirige vers la page du panier
//         return $this->redirectToRoute('cart_index');
//    }

//    #[Route("remove/{id}", name: 'app_remove')]
//    public function remove(Product $product, SessionInterface $session)
//    {
//     //on récupère l'id du produit
//     $id= $product->getId();

//     //on récupère le panier existant
//     $panier= $session->get('panier',[]);
        
//      //on retire le produit du panier s'il n'y a qu'un exemplaire
//      //sinon on décrémente sa quantité

//         if(!empty($panier[$id])){
//             if($panier[$id] > 1){
//             $panier[$id]--;
//         }else{
//             unset($panier[$id]);
//         }
//     }
//         $session->set('panier',$panier);
        
//         //on redirige vers la page du panier
//         return $this->redirectToRoute('cart_index');
//    }

//    #[Route("delete/{id}", name: 'app_delete')]
//    public function delete(Product $product, SessionInterface $session)
//    {
//     //on récupère l'id du produit
//     $id= $product->getId();

//     //on récupère le panier existant
//     $panier= $session->get('panier',[]);
        
//      //on retire le produit du panier s'il n'y a qu'un exemplaire
//      //sinon on décrémente sa quantité

//         if(!empty($panier[$id])){
//             unset($panier[$id]);
//         }
    
//         $session->set('panier',$panier);
        
//         //on redirige vers la page du panier
//         return $this->redirectToRoute('cart_index');
//    }

//    #[Route("empty", name: 'app_empty')]
//    public function empty(SessionInterface $session)
//    {
//        $session->remove('panier');

//        return $this->redirectToRoute('cart_index');
//    }


#[Route("/panier", name: 'cart_index')]
public function index(Cart $cart) : Response{

    $cart->getTotal();

    return $this->render('cart/index.html.twig',[
        'cart'=>$cart->getTotal()
    ]);


}

#[Route("/panier/add/{id}", name: 'cart_add')]
public function add(Cart $cart,$id) : Response{

    $cart->add($id);

    return $this->redirectToRoute('cart_index');
}

#[Route("/panier/remove/{id}", name: 'cart_remove')]
public function remove(Cart $cart,$id) : Response{

    $cart->remove($id);

    return $this->redirectToRoute('cart_index');
}


#[Route("/panier/delete/{id}", name: 'cart_delete')]
public function delete(Cart $cart,$id) : Response{

    $cart->delete($id);

    return $this->redirectToRoute('cart_index');
}

#[Route("/panier/empty", name: 'cart_empty')]
public function empty(Cart $cart) : Response{

    $cart->removeAll();

    return $this->redirectToRoute('cart_index');
}
}

