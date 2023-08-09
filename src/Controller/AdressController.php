<?php

namespace App\Controller;

use App\Form\AddressType;
use App\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Cart;

class AdressController extends AbstractController
{
    #[Route('/compte/adresses', name: 'app_address')]
    public function index(): Response
    {

        // dd($this->getUser());
        return $this->render('/account/address.html.twig', [
            'controller_name' => 'AdressController',
        ]);
    }

    #[Route('/compte/ajouter', name: 'app_address_add')]
    public function add(Request $request,EntityManagerInterface $entityManager): Response
    {
        $address= new Address();
        $form =$this->createForm(AddressType::class,$address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $address->setUser($this->getUser());
            // dd($address);

             $entityManager->persist($address);
             $entityManager->flush();

            
             return $this->redirectToRoute('app_address');
           
        }
      
        return $this->render('/account/address_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/compte/modifier{id}', name: 'app_address_edit')]
    public function edit(Request $request,EntityManagerInterface $entityManager ,$id): Response
    {
        $address= $entityManager->getRepository(Address::class)->Find($id);

        if(!$address || $address->getUser()!= $this->getUser()){
            return $this->redirectToRoute('app_address');
        }

        $form =$this->createForm(AddressType::class,$address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
   
             $entityManager->flush();
             return $this->redirectToRoute('app_address');
        }
      
        return $this->render('/account/address_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/compte/supprimer{id}', name: 'app_address_delete')]
    public function delete(EntityManagerInterface $entityManager,$id): Response
    {
        $address= $entityManager->getRepository(Address::class)->Find($id);

        if($address && $address->getUser()== $this->getUser()){
            $entityManager->remove($address);
            $entityManager->flush();
           
        }
             
           
          return $this->redirectToRoute('app_address');
      
    }

}