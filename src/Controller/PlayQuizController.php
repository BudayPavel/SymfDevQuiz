<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 24.02.2018
 * Time: 18:03
 */

namespace App\Controller;

use App\Entity\Result;
use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\User;
use App\Entity\Answer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class PlayQuizController extends Controller
{
    /**
     * @Route("/play", name="start")
     */
    public function beginquiz(Request $request)
    {
        return new Response($this->renderView('mainpage/beginQuiz.html.twig',['quiz'=>$this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id'=>$request->query->get('quiz')])]));
    }

    /**
     * @Route("/play/{slug}", name="play")
     */
    public function startQuiz(Request $request, $slug) {
        $quiz = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $slug]);
        $result = count($this->getDoctrine()->getRepository(Result::class)->findBy(['user_id' => $this->getUser()->getId(), 'quiz_id' => $slug]));
        if ($result === count($quiz->getQuestions())) {
            return $this->json(['cur' => 0],200);
        }
        if ($request->get('rem') != 'true') {
                $out = [];
                $out['quiz'] = $quiz->getName();
                $out['qid'] = $quiz->getId();
                $out['question'] = $quiz->getQuestions()[$result]->getText();
                $out['qud'] = $quiz->getQuestions()[$result]->getId();
                $out['answers'] = [];
                foreach ($quiz->getQuestions()[$result]->getAnswers() as $ans){
                    array_push($out['answers'], ['id' => $ans->getId(),'text' => $ans->getText()]);
                }
                $out['total'] = count($quiz->getQuestions());
                $out['cur'] = $result+1;
                return $this->json($out, 200);
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
                $next['question'] = $quiz->getQuestions()[(integer)$request->get('cur')]->getText();
                $next['cur'] = (integer)$request->get('cur') + 1;
                $next['answers'] =[];
                foreach ($quiz->getQuestions()[(integer)$request->get('cur')]->getAnswers() as $ans) {
                    array_push($next['answers'], ['id' => $ans->getId(), 'text' => $ans->getText()]);
                }
            } else {
                $next['cur'] = 0;
            }
            return $this->json($next,200);


        }
    }

    /**
     * @Route("/play/{slug}/top", name="top")
     */
    public function getTop(Request $request, $slug) {

        $quiz = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $slug]);
        $arr = [];
        $arr['rows'] = [];
        foreach ($this->getDoctrine()->getRepository(Result::class)->findQuizTop($quiz) as $top)
        {
            array_push($arr['rows'],(array)$top);
        }
        $arr['total'] = count($arr['rows']);
        $arr['current'] = 1;
        return $this->json($arr, 200);
    }

}
