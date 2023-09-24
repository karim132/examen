<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Form\AddressType;
use App\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class AdressController extends AbstractController
{
    #[Route('/compte/adresses', name: 'app_address')]
    public function index(): Response
    {
        return $this->render('/account/address.html.twig', [
            'controller_name' => 'AdressController',
        ]);
    }

    #[Route('/compte/ajouter', name: 'app_address_add')]
    public function add(Request $request,EntityManagerInterface $entityManager ,Cart $cart): Response
    {
        //création d'une nouvelle adresse
        $address= new Address();
        //création d'un nouveau formulaire
        $form =$this->createForm(AddressType::class,$address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            
            //set des données entrées dans le formulaire
            $address->setUser($this->getUser());
            dd($address);

             //fige les données entrées dans le formulaire
             $entityManager->persist($address);
             //enregistre en bdd
             $entityManager->flush();
             // si article dans le panier
             if ($cart->get()){
              //redirection vers la page commande
                return $this->redirectToRoute('app_order');
             }else{
             //sinon redirection vers la page adresse
             return $this->redirectToRoute('app_address');
             }
        }
      
        return $this->render('/account/address_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/compte/modifier{id}', name: 'app_address_edit')]
    public function edit(Request $request,EntityManagerInterface $entityManager ,$id): Response
    {
        // Recupère l'id de l'adresse en bdd
        $address= $entityManager->getRepository(Address::class)->Find($id);
        
        // si l'utilisateur n'a pas d'adresse ou une adresse différente de l'utilisateur en cours
        if(!$address || $address->getUser()!= $this->getUser()){
            //redirige vers ('app_address')
            return $this->redirectToRoute('app_address');
        }
        //Création d'un nouveau formulaire
        $form =$this->createForm(AddressType::class,$address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
             //enregistre les modifs en bdd
             $entityManager->flush();
              //redirige vers ('app_address')
             return $this->redirectToRoute('app_address');
        }
      
        return $this->render('/account/address_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/compte/supprimer{id}', name: 'app_address_delete')]
    public function delete(EntityManagerInterface $entityManager,$id): Response
    {
        // Recupère l'id de l'adresse en bdd
        $address= $entityManager->getRepository(Address::class)->Find($id);
        
        //si l'id de l'adresse recupéré et l'user récupéré de l'entité adress est égal à l'utilisateur en cours
        if($address && $address->getUser()== $this->getUser()){
           
            // supprime l'adresse
            $entityManager->remove($address);
            //supprime en bdd
            $entityManager->flush();           
        }
             // redirige vers('app_address')
          return $this->redirectToRoute('app_address');
      
    }

}