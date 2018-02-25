<?php

declare(strict_types=1);
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends Controller
{
    /**
     * @Route("/admin")
     */
    public function admin()
    {
        return new Response($this->renderView('admin/admin.html.twig'));
    }
}