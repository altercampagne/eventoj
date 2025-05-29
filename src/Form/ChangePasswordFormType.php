<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<string>
 */
class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank(),
                        new Length(
                            min: 7,
                            minMessage: 'Ton mot de passe doit faire au moins {{ limit }} caractères.',
                            // max length allowed by Symfony for security reasons
                            max: 4096,
                        ),
                    ],
                    'label' => 'Ton mot de passe',
                    'help' => 'Au minimum 7 caractères et si possible unique !',
                    'attr' => [
                        'placeholder' => 'Ton mot de passe',
                        'autocomplete' => 'new-password',
                    ],
                    'row_attr' => [
                        'class' => 'form-floating mb-3',
                    ],
                ],
                'second_options' => [
                    'label' => 'Ton mot de passe (confirmation)',
                    'attr' => [
                        'placeholder' => 'Ton mot de passe (confirmation)',
                        'autocomplete' => 'new-password',
                    ],
                    'row_attr' => [
                        'class' => 'form-floating mb-3',
                    ],
                ],
                'invalid_message' => 'Les mots de passe saisis doivent être identiques.',
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
