<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileUpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Ton adresse mail',
                'attr' => [
                    'placeholder' => 'Ton adresse mail',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => '<i class="ms-2 fa-solid fa-triangle-exclamation"></i> En cas de changement, ton adresse mail devra de nouveau être validée.',
                'help_html' => true,
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
