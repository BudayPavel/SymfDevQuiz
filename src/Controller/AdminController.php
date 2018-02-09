<?php

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use function Sodium\add;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\User;
use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Answer;
use Symfony\Component\Validator\Constraints\Choice;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(Request $request)
    {
        $task = new User();

        $form = $this->createFormBuilder($task)
            ->add('login')
            ->add('fname')
            ->add('lname')
            ->add('pass')
            ->add(
                'role',
                ChoiceType::class,
                array(
                    'choices' => array(
                        'Administrator' => 'ROLE_ADMIN',
                        'User' => 'ROLE_USER',
                    ),
                )
            )
            ->add(
                'active',
                ChoiceType::class,
                array(
                    'choices' => array(
                    'true' => '1',
                    'false' => '0',
                    ),
                    'expanded' => true,
                    'multiple' => false,
                )
            )

            ->add('save', SubmitType::class, array('label' => 'Submit'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->IsValid()) {
            $task = $form->getData();
            return $this->redirectToRoute('success');
        }

        return $this->render(
            'admin.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}
