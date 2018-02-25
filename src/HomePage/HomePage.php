<?php

namespace App\HomePage;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomePage extends Controller
{
    public function home()
    {
        return new Response($this->renderView('homepage/homepage.html.twig'));
    }
}
