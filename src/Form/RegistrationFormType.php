<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Ton adresse mail',
                'attr' => [
                    'placeholder' => 'georges.abitbol@gmail.com',
                ],
                'help' => 'Promis, pas de spam ! 😉',
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'Ton nom',
                'attr' => [
                    'placeholder' => 'Georges Abitbol',
                ],
                'required' => true,
                'help' => 'Nom, prénom, surnom, ... C\'est toi qui décides !',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Ton mot de passe',
                'attr' => [
                    'placeholder' => '***********',
                    'autocomplete' => 'new-password',
                ],
                'help' => 'Au minimum 7 caractères et si possible unique !',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'ai lu et j\'accepte les termes et conditions.',
                'help' => '💥 Pour le moment, rien n\'existe...',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
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
