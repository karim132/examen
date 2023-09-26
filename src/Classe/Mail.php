<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;


class Mail
{
    //clé Api
    private $api_key = '1c8ea07a618a717e6b9a7afdc17b3e20';
    //clé secrète API
    private $api_secret_key = 'bf930e86b42bf825f7eb346fdc835f22';

    public function send($to_email,$to_name,$subject,$content)
    {
        // On instancie l'objet mailjet dans $mj  et on lui fournit en paramètre la clé API, la clé secrète API et la version utilisée
        $mj= new Client($this->api_key, $this->api_secret_key, true,['version' => 'v3.1']);
        //création du body du mail
        $body = [
         'Messages' => [
          [
           //provenance de l'email 
            'From' => [
                'Email' => "karim.idir83@hotmail.fr",
                'Name' => "Karim"
            ],
              // destinataire
            'To' => [
                [
                    'Email' => $to_email,
                    'Name' => $to_name
                ] 
            ],
            //modèle d'email
            'TemplateID' => 5012209,
            'TemplateLanguage' => true,
            'Subject' => $subject,
            'Variables' => [
                'content' => $content ,
           ]
        ]
    ]
];
// Post du mail
$response = $mj->post(Resources::$Email, ['body' => $body]);
//reponse du mail
$response->success();
    }
}
