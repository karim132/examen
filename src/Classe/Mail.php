<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;


class Mail
{

    private $api_key = '1c8ea07a618a717e6b9a7afdc17b3e20';
    private $api_secret_key = 'bf930e86b42bf825f7eb346fdc835f22';

    public function send($to_email,$to_name,$subject,$content)
    {

        $mj= new Client($this->api_key, $this->api_secret_key, true,['version' => 'v3.1']);
        $body = [
         'Messages' => [
          [
            'From' => [
                'Email' => "karim.idir83@hotmail.fr",
                'Name' => "Karim"
            ],
            'To' => [
                [
                    'Email' => $to_email,
                    'Name' => $to_name
                ] 
            ],
            'TemplateID' => 5012209,
            'TemplateLanguage' => true,
            'Subject' => $subject,
            'Variables' => [
                'content' => $content ,
           ]
        ]
    ]
];
$response = $mj->post(Resources::$Email, ['body' => $body]);
$response->success();

    }
}
