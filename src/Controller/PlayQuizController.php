<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 24.02.2018
 * Time: 18:03
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class PlayQuizController extends Controller
{
    /**
     * @Route("/beginquiz")
     */
    public function beginquiz()
    {
        return new Response($this->renderView('mainpage/beginQuiz.html.twig'));
    }
    /**
     * @Route("/playquiz")
     */
    public function playquiz()
    {
        return new Response($this->renderView('mainpage/playQuiz.html.twig'));
    }
}
