<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 10.2.18
 * Time: 14.20
 */
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as TP;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', @TP\EmailType::class)
            ->add('firstName', @TP\TextType::class)
            ->add('lastName', @TP\TextType::class)
            ->add(
                'plainPassword',
                @TP\RepeatedType::class,
                array(
                'type' => @TP\PasswordType::class,
                'first_options'  => array('label' => ' '),
                'second_options' => array('label' => ' '),
                )
            )
            ->add('submit', @TP\SubmitType::class, array('label' => 'Sign Up'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
