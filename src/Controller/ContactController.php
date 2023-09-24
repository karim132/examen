<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Mailjet\Client;
use Mailjet\Resources;




class ContactController extends AbstractController
{
    private $api_key = '1c8ea07a618a717e6b9a7afdc17b3e20';
    private $api_secret_key = 'bf930e86b42bf825f7eb346fdc835f22';

    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        $form= new ContactType;
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        $mj= new Client($this->api_key, $this->api_secret_key, true,['version' => 'v3.1']);

        if ($form->isSubmitted() && $form->isValid()){

            if($this->getUser()){

              $data = $form->getData();
            //   dd($data);
            
             $content = $data['content'];
             $email = $data['email'];
             $firstname = $data['firstname'];
             $lastname = $data['lastname'];
           
            
           
            $body = [
             'Messages' => [
              [
                'From' => [
                    'Email' => "karim.idir83@hotmail.fr",
                    'Name' => "Karim"
                ],
                'To' => [
                    [
                        'Email' => "karim.idir83@hotmail.fr",
                        'Name' => "Karim"
                    ] 
                ],
                'Subject' => 'demande de contact',
                'HtmlPart' => "$content,<br>$lastname $firstname,<br>$email  "
            
            ]
        ]
    ];

    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success();

            }
            $this->addFlash(
                'success',
                'Nous avons bien reçu votre message.Nous vous contacterons bientôt!'
          );
        }
       
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
