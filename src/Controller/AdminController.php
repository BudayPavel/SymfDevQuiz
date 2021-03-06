<?php
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
     * @Route("/administrator", name="administrator")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function admin()
    {
        return new Response($this->renderView('admin.html.twig'));
    }
}
