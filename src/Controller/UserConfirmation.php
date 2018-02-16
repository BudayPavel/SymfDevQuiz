<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class UserConfirmation extends Controller
{
    public function indexAction( \Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('2345pawel@gmail.com')
            ->setTo('2345pawel@mail.ru')
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'emails/registration.html.twig'),
                'text/html'
            )
        ;

        $mailer->send($message);

        return new Response($this->renderView('emails/mailsent.html.twig'));
    }
    //in file .env add - MAILER_URL=gmail://2345pawel:p2a3w4e5l@localhost?encryption=tls&auth_mode=oauth
    //composer require mailer
}