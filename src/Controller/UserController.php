<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 11.2.18
 * Time: 16.39
 */

declare(strict_types=1);

namespace App\Controller;

use App\Form\UserLogin;
use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/authorize", name="authorize")
     */
    public function show(
        Request $request,
        AuthenticationUtils $authUtils

    ) {
        $rform = $this->createForm(UserType::class);
        $lform = $this->createForm(UserLogin::class);

        $error = $authUtils->getLastAuthenticationError();

        return $this->render(
            'registration/authorize.html.twig',
            array(
                'rform' => $rform->createView(),
                'lform' => $lform->createView(),
                'error' => $error,
            )
        );
    }

    /**
     * @Route("/authorize/signup", name="signup_user")
     */
    public function signUp(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword(
                $user,
                $user->getPlainPassword()
            );
            $user->setPassword($password);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('useradded');
        }

    }

    /**
     * @Route("/authorize/signin", name = "signin_user")
     */
    public function signIn()
    {

    }

    /**
     * @Route("/admin")
     */
    public function admin()
    {
        return new Response('<html><body>Admin page!</body></html>');
    }
}