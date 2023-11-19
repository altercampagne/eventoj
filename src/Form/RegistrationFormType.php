<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Ton adresse mail',
                'help' => 'Promis, pas de spam ! 😉',
                'attr' => [
                    'placeholder' => 'Ton adresse mail',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Ton nom',
                'help' => 'Nom, prénom, surnom, ... C\'est toi qui décides !',
                'attr' => [
                    'placeholder' => 'Ton nom',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Ton mot de passe',
                'help' => 'Au minimum 7 caractères et si possible unique !',
                'attr' => [
                    'placeholder' => 'Ton mot de passe',
                    'autocomplete' => 'new-password',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegistrationFormDTO::class,
        ]);
    }
}
