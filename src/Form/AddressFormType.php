<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @extends AbstractType<Address>
 */
class AddressFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('countryCode', CountryType::class, [
                'label' => 'Pays',
                'attr' => [
                    'placeholder' => 'Ton pays',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'preferred_choices' => ['FR', 'BE'],
            ])
            ->add('addressLine1', TextType::class, [
                'label' => 'Adresse',
                'required' => $options['address_line1_required'],
                'attr' => [
                    'placeholder' => 'Ton adresse',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'constraints' => $options['address_line1_required'] ? new Assert\NotBlank() : [],
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
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => 'Ton code postal',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
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
            'address_line1_required' => true,
        ]);
        $resolver->setAllowedTypes('address_line1_required', 'bool');
    }
}
