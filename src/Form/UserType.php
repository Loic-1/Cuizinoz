<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('email')
            // ->add('roles')
            // ->add('password')
            // ->add('isVerified')
            ->add('biography')
            // ->add('profilePicture')
            ->add('pseudo')
            // ->add('registrationDate', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('alt')
            // ->add('followers', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
            // ->add('followees', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
