<?php

namespace App\Classe;
use Mailjet\Client;
use Mailjet\Resources;


class Mail
{
    private $api_key= '1c33301448ae8eebdbba60639ce92bbd';
    private $api_key_secret= '002bd7685121d86153ca0ac94fd2910b';

    public function send($to_email,$to_name,$subject,$content)
    {
        $mj= new Client($this->api_key,$this->api_key_secret,true,['version'=>'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "raoiliarison.dev@gmail.com",
                        'Name' => "Madakitchen"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'templateID' => 4968561,
                    'templateLanguage'=> true,
                    'Subject'=> $subject,
                    'Variables'=>[
                        'content'=> $content
                    ]
                ]
            ]
        ];
        
        

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        
        // Lire response
        
        $response->success();
    }

}