<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<User>
 */
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
            ->add('address', AddressFormType::class)
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
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Ton mot de passe',
                'help' => 'Au minimum 7 caractères et si possible unique !',
                'attr' => [
                    'placeholder' => 'Ton mot de passe',
                    'autocomplete' => 'new-password',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 7,
                        'minMessage' => 'Ton mot de passe doit faire au moins {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
