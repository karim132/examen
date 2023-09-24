<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Security\LoginFormAuthenticator;



class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_registration')]
    public function index(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator,
        EntityManagerInterface $entityManager
        ): Response
    {
        // création d'un nouvel utilisateur
        $user = new User();
        //création d'un  formulaire
        $form = $this->createForm(RegistrationFormType::class, $user);
        //gestion du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            // Va chercher en BDD si il y a un utilisateur déja crée avec le mail
            $search_email= $entityManager->getRepository(User::class)->FindOneBy([
                'email'=>$user->getEmail()]);

             // hache le mot de passe (basé sur la configuration security.yaml pour la classe $user)
             if(!$search_email){
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                // fige les données récupérées dans l'objet $user
                $entityManager->persist($user);
                //enregistre en base de données
                $entityManager->flush();
                
                $this->addFlash(
                    'success',
                    'Votre compte a bien été créé'
               );

               $mail = new Mail();
               // $content ="Bienvenue ".$user->getFirstname()."<br/>Vous pouvez dès à présent visiter notre site";
               $mail->send($user->getEmail(),$user->getFirstname(),subject:'Bienvenue sur notre site',
               content:"Bienvenue ".$user->getFirstname()."<br/>Vous pouvez dès à présent visiter notre site");
                // si formulaire valide, authentification de l'utilisateur
                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request                   
                );              
        }
    }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
