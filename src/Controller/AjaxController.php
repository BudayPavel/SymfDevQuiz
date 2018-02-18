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
use function MongoDB\BSON\fromJSON;
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
            $repository->countFiltered($params);

            $arr = $repository->findAllFiltered1($params);

            $output = array(
                'current' => $params['current_page_number'],
                'rowCount' => $params['records_per_page'],
                'total' => $repository->countFiltered($params),
                'rows' => $arr
            );


            break;
        }
            return $this->json($output, Response::HTTP_OK, array('Type' => 'User'));
    }
}