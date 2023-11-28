<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationAddressFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
