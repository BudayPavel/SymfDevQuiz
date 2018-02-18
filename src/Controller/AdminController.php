<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 13.02.2018
 * Time: 10:56
 */
declare(strict_types=1);
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class AdminController extends Controller
{
    /**
     * @Route("/administrator")
     *
     */
    public function admin()
    {
        return new Response($this->renderView('agtemp.html.twig'));
    }
}