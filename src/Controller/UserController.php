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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Route("/authorize", name="authorize")
     */
    public function show(Request $request) {

        if (!is_null($this->getUser()))
        {
            return $this->redirectToRoute('/');
        }

        $rform = $this->createForm(UserType::class);
        $lform = $this->createForm(UserLogin::class);

        return $this->render(
            'registration/auth.html.twig',
            array(
                'rform' => $rform->createView(),
                'lform' => $lform->createView(),
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

            return $this->redirectToRoute('authorize');
        }

        return new Response('ssss');
    }

    /**
     * @Route("/admin")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function admin()
    {
        return new Response('<html><body>Admin page!</body></html>');
    }
}