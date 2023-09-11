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
    public function index(Request $request,UserPasswordHasherInterface $userPasswordHasher,UserAuthenticatorInterface $userAuthenticator,LoginFormAuthenticator $authenticator,EntityManagerInterface $entityManager): Response
    {

        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            // $user= $form->getData();

            $search_email= $entityManager->getRepository(User::class)->FindOneBy([
                'email'=>$user->getEmail()]);

             if(!$search_email){
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
    
                $entityManager->persist($user);
                $entityManager->flush();

                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );

                $mail = new Mail();
                // $content ="Bienvenue ".$user->getFirstname()."<br/>Vous pouvez dès à présent visiter notre site";
                $mail->send($user->getEmail(),$user->getFirstname(),subject:'Bienvenue sur notre site',
                content:"Bienvenue ".$user->getFirstname()."<br/>Vous pouvez dès à présent visiter notre site");
             }
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
 