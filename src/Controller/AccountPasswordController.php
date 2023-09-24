<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{
    #[Route('/compte/password-modify', name: 'app_account_password')]
    public function index(Request $request,
    UserPasswordHasherInterface $userPasswordHasher,
    EntityManagerInterface $entityManager
    ): Response

    {    
        // récupère les data de l'utilistaeur en cours dont le mdp   
        $user= $this->getUser();
        // création d'un nouveau formulaire
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

                //Recupération du nouveau mdp dans la variable $new
                $new= $form->get('new_password')->getData();
             
                // hache le mot de passe 
             $password= $userPasswordHasher->hashPassword($user,$new);
          
             //set le mdp 
            $user->setPassword($password);

            // fige les données récupérées dans l'objet $user
            $entityManager->persist($user);
            //enregistre en base de données
            $entityManager->flush();
          
            $this->addFlash(
                'success',
                'Votre mot de passe a bien été mis à jour!'
           );
        }
        //  }
        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            // 'notification'=>$notification
        ]);
    }
}
