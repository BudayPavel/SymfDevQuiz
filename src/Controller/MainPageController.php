<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 23.02.2018
 * Time: 17:24
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends Controller
{
    /**
     * @Route("/mainpage")
     */
    public function mainpage()
    {
        return new Response($this->renderView('mainpage/main.html.twig'));
    }
}
