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

class QuizViewController extends Controller
{
    /**
     * @Route("/quizView")
     */
    public function quizView()
    {
        return new Response($this->renderView('mainpage/quizView.html.twig'));
    }
}
