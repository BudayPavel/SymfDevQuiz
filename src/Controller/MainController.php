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


class MainController extends Controller
{

    /**
     * @Route("", name="main")
     */
    public function showAll(Request $request) {
        return $this->render('mainpage/main.html.twig');
    }

//    /**
//     * @Route("/sqltest", name="mainaaa")
//     */
//    public function sql() {
////        $arr = $this->getDoctrine()->getRepository(Result::class)->findQuizTop(
////            $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id'=>16])
////        );
//
//        $arr = $this->getDoctrine()->getRepository(User::class)->findQuizRes();
//
//        return $this->json($arr, 200);
//    }

    /**
     * @Route("/play/{slug}", name="play")
     */
    public function startQuiz(Request $request, $slug) {
        $quiz = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $slug]);
        $result = count($this->getDoctrine()->getRepository(Result::class)->findBy(['user_id' => $this->getUser()->getId(), 'quiz_id' => $slug]));
        if ($request->get('rem') != 'true') {
            if ($result === count($quiz->getQuestions())) {
                return $this->render('temp.html.twig', array(
                    'done' => true
                ));
            } else {
                return $this->render('temp.html.twig', array(
                        'cur' => $result+1,
                        'done' => false,
                        'qid' => $slug,
                        'quiz' => $quiz,
                        'question' => $quiz->getQuestions()[$result],
                    )
                );
            }
        } else {
            if ($request->get('aid') != null) {
                $em = $this->getDoctrine()->getManager();
                $result = new Result();
                $result->setQuizId($this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $slug]));
                $result->setQuestionId($this->getDoctrine()->getRepository(Question::class)->findOneBy(['id' => $request->get('qud')]));
                $result->setAnswerId($this->getDoctrine()->getRepository(Answer::class)->findOneBy(['id' => $request->get('aid')]));
                $result->setUserId($this->getUser());
                $result->setTime((integer)$request->get('time'));
                $em->persist($result);
                $em->flush();
            } else {
                return $this->json(['cur' => -1], 200);
            }

            $next = array();

            if ($quiz->getQuestions()[(integer)$request->get('cur')] != null) {
                $next['question'] = $quiz->getQuestions()[(integer)$request->get('cur')];
                $next['cur'] = (integer)$request->get('cur') + 1;
                $next['answers'] = $quiz->getQuestions()[(integer)$request->get('cur')]->getAnswers();
            } else {
                $next['cur'] = 0;
            }
            return $this->json($next,200);


        }
    }


}
