<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Result;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomePage extends Controller
{
    /**
     * @Route("/hello", name="hello")
     *
     */
    public function home()
    {
        $users = $this->getDoctrine()->getRepository(Result::class)->findTop();
        return new Response($this->renderView('homepage/home.html.twig', ['users'=>$users]));
    }
}
