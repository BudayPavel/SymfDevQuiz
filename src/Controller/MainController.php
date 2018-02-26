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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class MainController extends Controller
{
    /**
     * @Route("", name="main")
     *
     */
    public function showAll(Request $request)
    {
        if ($this->getUser() != null) {
            return $this->render('main.html.twig');
        } else {
            return $this->redirectToRoute('hello');
        }
    }

    /**
     * @Route("/myquiz", name="myQuiz")
     * @Security("has_role('ROLE_USER')")
     */
    public function sql()
    {
        return $this->render('results.html.twig');
    }

    /**
     * @Route("/replay/{slug}", name="replay")
     * @Security("has_role('ROLE_USER')")
     */
    public function restartQuiz(Request $request, $slug)
    {
        try {
            $result = $this->getDoctrine()->getRepository(Result::class)->findBy(['user_id' => $this->getUser()->getId(), 'quiz_id' => $slug]);
            $em = $this->getDoctrine()->getManager();
            foreach ($result as $res) {
                $em->remove($res);
            }
            $em->flush();

            $router = $this->get('router');
            $uri = $router->generate('start', array('quiz' => $slug));
            return new RedirectResponse($uri);
        } catch (\Exception $e) {
            return new Response($this->renderView(
                'mainpage/finishReg.html.twig',
                array('mes_one' => "Error!",
                    'mes_two' => "This page doesn't exist")
            ));
        }
    }
}
