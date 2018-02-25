<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Result;
use App\Entity\Quiz;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;


class MainController extends Controller
{

    /**
     * @Route("", name="main")
     */
    public function showAll(Request $request) {
        return $this->render('main.html.twig');
    }

    /**
     * @Route("/myquiz", name="myQuiz")
     */
    public function sql() {

        return $this->render('results.html.twig');
    }

    /**
     * @Route("/replay/{slug}", name="replay")
     */
    public function restartQuiz(Request $request, $slug)
    {
        $result = $this->getDoctrine()->getRepository(Result::class)->findBy(['user_id' => $this->getUser()->getId(), 'quiz_id' => $slug]);
        $em = $this->getDoctrine()->getManager();
        foreach ($result as $res) {
            $em->remove($res);
        }
        $em->flush();

        $router = $this->get('router');
        $uri = $router->generate('start', array('quiz' => $slug));
        return new RedirectResponse($uri);
    }

    /**
     * @Route("/test")
     */
    public function test()
    {
        $repository = $this->getDoctrine()->getRepository(Result::class);
        $arr = $repository->findTop();

        return $this->json($arr,200);
    }
}
