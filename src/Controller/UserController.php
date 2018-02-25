<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\PasRecovery;
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
            return $this->redirectToRoute('main');
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
        Request $request,
        \Swift_Mailer $mailer
    ) {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $arr = $repository->findOneBy(['email' => $request->get('pass_restore')['email']]);
        if ($arr != null) {
            $message = (new \Swift_Message('Password Recovery'))
                ->setFrom('2345pawel@gmail.com')
                ->setTo($request->get('pass_restore')['email'])
                ->setBody(
                    $this->renderView(
                        'emails/recovery_pas.html.twig',
                        array('email' => $request->get('pass_restore')['email'])),
                    'text/html');
            $mailer->send($message);
            $em = $this->getDoctrine()->getManager();
            $arr->setActiveRes(false);
            $em->flush();
            return new Response("",200);
        }
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
                return $this->redirectToRoute('hello');
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

    /**
     * @Route("/recovery", name="recovery")
     */
    public function form_rec(Request $request) {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $request->query->get('email')]);
        if ($user->isActiveRes()){
            return $this->redirectToRoute('hello');
        }
        $form = $this->createForm(PasRecovery::class);

        $router =$this->get('router');
        $uri = $router->generate('recovery_password', array('email' => $request->query->get('email')));

        return $this->render(
            'recoverypas.html.twig',
            array(
                'form' => $form->createView(),'mes_two' => "", 'action' => $uri
            )
        );
    }

    /**
     * @Route("/recovery/password", name="recovery_password")
     */
    public function recoverypas(Request $request,
                                UserPasswordEncoderInterface $passwordEncoder){
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $request->query->get('email')]);

        $em = $this->getDoctrine()->getManager();

        if ($request->get('pas_recovery')['plainPassword']['first'] === $request->get('pas_recovery')['plainPassword']['second']) {
            $user->setPassword($passwordEncoder->encodePassword($user, $request->get('pas_recovery')['plainPassword']['first']));
            //            return $this->JSON(['l'=>$request->get('user')['plainPassword']['first']], 200);
            $user->setActiveRes(true);
            $em->flush();
            return new Response($this->renderView('mainpage/finishReg.html.twig',
                array('mes_one' => "Поздарвляем!",
                    'mes_two' => "Вы успешно обнавили пороль")));
        } else {
            return new Response($this->renderView('recoverypas.html.twig',
                array('mes_two' => "Error...")));
        }
    }
}