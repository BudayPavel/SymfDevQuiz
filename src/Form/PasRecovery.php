<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as TP;


class PasRecovery extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'plainPassword',
                @TP\RepeatedType::class,
                array(
                    'type' => @TP\PasswordType::class,
                    'first_options'  => array('label' => ' '),
                    'second_options' => array('label' => ' '),
                )
            )
            ->add('submit', @TP\SubmitType::class, array('label' => 'reestablish'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
