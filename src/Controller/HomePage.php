<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomePage extends Controller
{
    /**
     * @Route("/hello")
     *
     */
    public function home()
    {
        return new Response($this->renderView('homepage/home.html.twig'));
    }
}