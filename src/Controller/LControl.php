<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 11.2.18
 * Time: 14.55
 */

namespace App\Controller;

use App\Form\UserLogin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;

class LControl extends Controller
{

    /**
     * @Route("/signin", name = "signin")
     */
    public function signIn(Request $request, AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();

        $lastUsername = $authUtils->getLastUsername();

        $form = $this->createForm(UserLogin::class);
        return $this->render(
            'registration/login.html.twig',
            array(
                'form' => $form->createView(),
                'error' => $error,
                'lastUsername' => $lastUsername
            )
        );
    }

    /**
     * @Route("/admin")
     */
    public function admin()
    {
        return new Response('<html><body>Admin page!</body></html>');
    }
}