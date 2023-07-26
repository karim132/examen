<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{
    #[Route('/compte/password-modify', name: 'app_account_password')]
    public function index(Request $request,UserPasswordHasherInterface $userPasswordHasher,UserAuthenticatorInterface $userAuthenticator,EntityManagerInterface $entityManager): Response

    {
        $notification= null;

        $user= $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $old = $form->get('old_password')->getData();

            // dd($old);

            // if ($userAuthenticator->$passwordisValid($user,$old)){
                $new= $form->get('new_password')->getData();
                // dd($user);

             $password= $userPasswordHasher->hashPassword($user,$new);
                // dd($user);
                    
            
            $user->setPassword($password);
        
            //   dd($user)
            ;

            $entityManager->persist($user);
            $entityManager->flush();
            $notification='Votre mot de passe a bien été mis à jour';
        }
        //  }
        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification'=>$notification
        ]);
    }
}
