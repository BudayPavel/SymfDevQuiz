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
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        UserPasswordEncoderInterface $passwordEncoder,
        ValidatorInterface $validator
    ) {
        $user = new User();
        $user->setEmail($request->get('user')['email']);
        $user->setFirstName($request->get('user')['firstName']);
        $user->setLastName($request->get('user')['lastName']);
        if ($request->get('user')['plainPassword']['first'] === $request->get('user')['plainPassword']['second']) {
            $user->setPassword($passwordEncoder->encodePassword($user, $request->get('user')['plainPassword']['first']));
        } else {
            return new Response("Password doesn't match",400);
        }

        $errors = $validator->validate($user);

        if (!count($errors) > 0) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('authorize');
        }

        return new Response("<H1>Email already taken</H1>", 400);
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