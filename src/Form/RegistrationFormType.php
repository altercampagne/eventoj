<?php

declare(strict_types=1);

namespace App\Form;

use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
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
            ->add('firstName', TextType::class, [
                'label' => 'Ton prénom',
                'attr' => [
                    'placeholder' => 'Ton prénom',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Ton nom',
                'attr' => [
                    'placeholder' => 'Ton nom',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('birthDate', BirthdayType::class, [
                'required' => true,
                'widget' => 'single_text',
                'label' => 'Ta date de naissance',
                'attr' => [
                    'placeholder' => 'Ta date de naissance',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'input' => 'datetime_immutable',
            ])
            ->add('countryCode', CountryType::class, [
                'label' => 'Ton pays',
                'attr' => [
                    'placeholder' => 'Ton pays',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'preferred_choices' => ['FR', 'BE'],
            ])
            ->add('addressLine1', TextType::class, [
                'label' => 'Ton addresse',
                'attr' => [
                    'placeholder' => 'Ton address',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('addressLine2', TextType::class, [
                'label' => 'Complément d\'adresse (facultatif)',
                'attr' => [
                    'placeholder' => 'Complément d\'adresse',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'required' => false,
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'Ton code postal',
                'attr' => [
                    'placeholder' => 'Ton code postal',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ta ville',
                'attr' => [
                    'placeholder' => 'Ta ville',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('phoneNumber', PhoneNumberType::class, [
                'format' => PhoneNumberFormat::NATIONAL,
                'default_region' => 'FR',
                'label' => 'Ton numéro de téléphone',
                'attr' => [
                    'placeholder' => 'Ton numéro de téléphone',
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
