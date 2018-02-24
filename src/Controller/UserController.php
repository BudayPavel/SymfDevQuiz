<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\PassRestore;
use App\Form\UserLogin;
use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
        $pform = $this->createForm(PassRestore::class);

        return $this->render(
            'auth.html.twig',
            array(
                'rform' => $rform->createView(),
                'lform' => $lform->createView(),
                'pform' => $pform->createView(),
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
        if (isset($request->get('user')['role'])) {
            $user->setRoles($request->get('user')['role']);
        }
//        if (isset($request->get('user')['active']) && $request->get('user')['active'] === 'on') {
//            $user->setActive(true);
//        }
        if ($request->get('user')['plainPassword']['first'] === $request->get('user')['plainPassword']['second']) {
            $user->setPassword($passwordEncoder->encodePassword($user, $request->get('user')['plainPassword']['first']));
        } else {
            return $this->json(['errorMes'=>'Passwords does not match'], 400);
        }

        $errors = $validator->validate($user);

        if (!count($errors) > 0) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new JsonResponse(['success'=>'Success! Check your email!'],200);
        }

        return new JsonResponse(['errorMes' => 'Email already used'],400);
    }

    /**
     * @Route("/authorize/forget", name="forget")
     */
    public function restorePass(
        Request $request
    ) {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $arr = $repository->findBy(['email' => $request->get('pass_restore')['email']]);

        if (count($arr) == 1) {
            //send mail
            echo 'true';
            return new Response("",200);
        }
        echo 'fllase';
        return new Response("", 400);
    }
}