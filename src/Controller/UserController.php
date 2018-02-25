<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\PassRestore;
use App\Form\UserLogin;
use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        ValidatorInterface $validator,
        \Swift_Mailer $mailer
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
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('2345pawel@gmail.com')
                ->setTo($request->get('user')['email'])
                ->setBody(
                    $this->renderView(
                        'emails/registration.html.twig',
                        array('name' => $request->get('user')['firstName'],
                              'hash' => $user->getPassword())),
                    'text/html');
            $mailer->send($message);

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
    /**
     * @Route("/finishreg", name="finishreg")
     */
    public function finishreg(Request $request){
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['password' => $request->query->get('hash')]);
        $em = $this->getDoctrine()->getManager();
        if ($user != null){
            if ($user->isActive()){
                return new RedirectResponse('/');
            }else{
                $user->setActive(true);
                $em->flush();
                return new Response($this->renderView('mainpage/finishReg.html.twig',
                    array('mes_one' => "Поздравляем",
                          'mes_two' => "Вы успешно прошли регистрацию. Для прохождения викторины перейдите на главную страницу.")));
            }
        }else{
            return new Response($this->renderView('mainpage/finishReg.html.twig',
                array('mes_one' => "Ошибка!",
                    'mes_two' => "Такой ссылки не существет.")));
        }

    }
}