<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 13.2.18
 * Time: 15.07
 */

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\User;
use App\Repository\UserRepository;
use function MongoDB\BSON\fromJSON;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AjaxController extends Controller
{
    /**
     * @Route("/ajax/{slug}")
     *
     */
    public function getJson(Request $request, $slug)
    {
        $params = array(
            'start' => 0,
            'current_page_number' => 1,
            'records_per_page' => 10,
            'search' => "",
            'searchFields' => null,
            'order' => 'ASC',
            'orderField' => ""
        );

        $params['current_page_number'] = $request->get('current');
        $params['records_per_page'] = $request->get('rowCount');
        $params['start'] = ($params['current_page_number'] - 1) * $params['records_per_page'];
        $params['search'] = $request->get('searchPhrase');
        $params['searchFields'] = $request->get('searchableFields');
        $params['orderField'] = $request->get('orderField');
        $params['order'] = $request->get('order');

        switch ($slug) {
        case 'users':
            $repository = $this->getDoctrine()->getRepository(User::class);
            $arr = $repository->findAllFiltered($params);
            break;
        case 'quiz':
            $repository = $this->getDoctrine()->getRepository(Quiz::class);
            $arr = $repository->findAllFiltered($params);
            break;
        case 'question':
            $repository = $this->getDoctrine()->getRepository(Question::class);
            $arr = $repository->findAllFiltered($params);
            break;
        case 'answer':
            $repository = $this->getDoctrine()->getRepository(Answer::class);
            $arr = $repository->findAllFiltered($params);
            break;
        }
            $output = array(
                'current' => $params['current_page_number'],
                'rowCount' => $params['records_per_page'],
                'total' => $repository->countFiltered($params),
                'rows' => $arr
            );
            return $this->json($output, Response::HTTP_OK, array('Type' => 'User'));
    }

    /**
     * @Route("/ajax/users/{action}")
     *
     */
    public function userActions(Request $request, $action, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator)
    {
        $params = array();
        $params['id'] = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['id' => $params['id']]);

        if (!$user && $action != 'add') {
            return $this->json(['errorMes' => 'User not found!'], 400);
        }

        switch ($action) {
            case 'delete':
                $em->remove($user);
                break;
            case 'update':
                $user->setEmail($request->get('user')['email']);
                $user->setFirstName($request->get('user')['firstName']);
                $user->setLastName($request->get('user')['lastName']);
                $user->setRoles($request->get('user')['role']);
                if (!isset($request->get('user')['active'])) {
                    $user->setActive(false);
                } else {
                    $user->setActive(true);
                }
                $em->flush();
                break;
            case 'add':
                $user = new User();
                $user->setEmail($request->get('user')['email']);
                $user->setFirstName($request->get('user')['firstName']);
                $user->setLastName($request->get('user')['lastName']);
                $user->setRoles($request->get('user')['role']);
                $user->setActive($request->get('user')['active']);
                $user->setRoles($request->get('user')['role']);
                if (!isset($request->get('user')['active'])) {
                    $user->setActive(false);
                } else {
                    $user->setActive(true);
                }

                if ($request->get('user')['plainPassword']['first'] === $request->get('user')['plainPassword']['second']) {
                    $user->setPassword($passwordEncoder->encodePassword($user, $request->get('user')['plainPassword']['first']));
                } else {
                    return $this->json(['errorMes' => 'Passwords does not match'], 400);
                }
                $errors = $validator->validate($user);

                if (!count($errors) > 0) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    return new JsonResponse(['success' => 'Success'], 200);
                }

                return new JsonResponse(['errorMes' => 'Email already used'], 400);
                break;
            case 'setpass':
                break;
        }
        $em->flush();
        return $this->json(['result' => 'success'], Response::HTTP_OK);
    }

    /**
     * @Route("/ajax/question/{action}")
     *
     */
    public function questionActions(Request $request, $action)
    {
        $params = array();
        $params['id'] = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $question = $em->getRepository(Question::class)->findOneBy(['id' => $params['id']]);

        if (!$question && $action != 'add' && $action != 'get') {
            return $this->json(['errorMes' => 'Question not found!'], 400);
        }

        switch ($action) {
            case 'get':
                $search = ['searchFields' => ['text'],
                    'search' => $request->get('search'),
                    'records_per_page' => 5,
                    'start' => 0
                ];
                return $this->json($em->getRepository(Question::class)->findAllFiltered($search));
                break;
            case 'sub':
                $answers = $em->getRepository(Answer::class)->findJoinedAnswers($params['id']);
                return $this->json($answers, Response::HTTP_OK);
                break;
            case 'delete':
                $em->remove($question);
                break;
            case 'add':
                if (null === $request->get('answers')) {
                    return $this->json(['errorMes' => 'Need at least 1 answer'], Response::HTTP_BAD_REQUEST, array('Type' => 'Question'));
                }
                $right = 0;
                foreach ($request->get('answers') as $ans) {
                    if ($ans['correct'] === 'true') {
                        $right++;
                    }
                }
                if ($right != 1) {
                    return $this->json(['errorMes' => 'Only 1 right answer required'], Response::HTTP_BAD_REQUEST, array('Type' => 'Question'));
                }
                $question = new Question();
                $question->settext($request->get('text'));

                foreach ($request->get('answers') as $ans) {
                    $answer = new Answer();
                    $answer->settext($ans['text']);
                    if ($ans['correct'] === 'true') {
                        $answer->setright(true);
                    } else {
                        $answer->setright(false);
                    }
                    $answer->setQuestion($question);
                    $question->getAnswers()->add($answer);
                }
                $em->persist($question);
                break;
            case 'update':
                if (null === $request->get('answers')) {
                    return $this->json(['errorMes' => 'Need at least 1 answer'], Response::HTTP_BAD_REQUEST, array('Type' => 'Question'));
                }
                $right = 0;
                foreach ($request->get('answers') as $ans) {
                    if ($ans['correct'] === 'true') {
                        $right++;
                    }
                }
                if ($right != 1) {
                    return $this->json(['errorMes' => 'Only 1 right answer required'], Response::HTTP_BAD_REQUEST, array('Type' => 'Question'));
                }
                $answers = $em->getRepository(Answer::class)->findJoinedAnswers($params['id']);
                foreach ($answers as $ans) {
                    $em->remove($ans);
                }

                $question->settext($request->get('text'));

                foreach ($request->get('answers') as $ans) {
                    $answer = new Answer();
                    $answer->settext($ans['text']);
                    if ($ans['correct'] === 'true') {
                        $answer->setright(true);
                    } else {
                        $answer->setright(false);
                    }
                    $answer->setQuestion($question);
                    $question->getAnswers()->add($answer);
                }

                break;
        }
        $em->flush();
        return $this->json(['result' => 'success'], Response::HTTP_OK);
    }

    /**
     * @Route("/ajax/quiz/{action}")
     *
     */
    public function quizActions(Request $request, $action)
    {
        $params = array();
        $params['id'] = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $quiz = $em->getRepository(Quiz::class)->findOneBy(['id' => $params['id']]);
//        if (!$quiz && $action != 'add') {
//            return $this->json(['errorMes' => 'Quiz not found!'], 400);
//        }

        switch ($action) {
            case 'add':
                if (count($request->get('questions')) === 0) {
                    return $this->json(['errorMes' => 'Need at least 1 question!'], 400);
                }
                $quiz = new Quiz();
                $quiz->setname($request->get('text'));
                $quiz->setdate(date("d.m.Y"));
                $quiz->setactive(true);

                foreach ($request->get('questions') as $q) {
                    $question = $em->getRepository(Question::class)->findOneBy(['id' => $q]);
                    $quiz->getQuestions()->add($question);
                }

                $em->persist($quiz);
                break;
            case 'update':
                $quiz->getQuestions()->clear();
                $quiz->setname($request->get('text'));
                $quiz->setactive($request->get('active') === 'true' ? true : false);
                //return $this->json(['success' => $quiz->getactive()], Response::HTTP_OK);
                foreach ($request->get('questions') as $q) {
                    $question = $em->getRepository(Question::class)->findOneBy(['id' => $q]);
                    $quiz->getQuestions()->add($question);
                }
                break;
            case 'delete':
                $em->remove($quiz);
                break;
            case 'sub':
                $arr = $em->getRepository(Quiz::class)->findOneBy(['id' => $params['id']]);
                $out = [];
                foreach ($arr->getQuestions() as $q) {
                    array_push($out, ['id' => $q->getId(), 'text' => $q->getText()]);
                }
                return $this->json($out, 200);
                break;
        }
        $em->flush();
        return $this->json(['result' => 'success'], Response::HTTP_OK);
    }
}