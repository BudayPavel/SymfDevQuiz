<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 13.2.18
 * Time: 15.07
 */

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AjaxController extends Controller
{
    /**
     * @Route("/ajax/{slug}")
     *
     */
    public function getJson(Request $request, $slug)
    {
            $params = array(
                'rowsPerPage' => $request->query->get('rowsPerPage'),
                'sortBy' => $request->query->get('sortBy'),
                'order' => $request->query->get('order'),
                'filterBy' => $request->query->get('filterBy'),
                'pattern' => $request->query->get('pattern'),
                'page'=> $request->query->get('page')
            );

            switch ($slug) {
            case 'users':
                $repository = $this->getDoctrine()->getRepository(User::class);
                $arr = $repository->findAll();
//                ByPattern(
//                        array('filter' =>$params['filterBy'] , 'pattern' => $params['pattern']),
//                        array('sort' => $params['sortBy'] , 'order' => $params['order']) // not error, phpstorm can't see method
//                    );

                break;
            }

            return $this->json($arr, Response::HTTP_OK, array('Type' => 'User'));
    }
}