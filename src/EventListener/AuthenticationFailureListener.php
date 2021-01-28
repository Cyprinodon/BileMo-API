<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;

class AuthenticationFailureListener
{
    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $data = [
            'message' => 'Identifiants incorrects, veuillez vérifier la validité des valeurs pour les clefs \'name\' et \'password\'.',
        ];

        $response = new JWTAuthenticationFailureResponse($data,401);

        $event->setResponse($response);
    }
}