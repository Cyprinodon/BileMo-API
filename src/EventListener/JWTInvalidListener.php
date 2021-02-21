<?php


namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTFailureEventInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;

class JWTInvalidListener
{
    /**
     * @param JWTFailureEventInterface $event
     */
    public function onJWTInvalid(JWTFailureEventInterface $event)
    {
        $data = [
            'message' => 'Votre token n\'est pas ou plus valide, connectez-vous pour en obtenir un nouveau.'
        ];

        $response = new JWTAuthenticationFailureResponse($data, 403);

        $event->setResponse($response);
    }
}