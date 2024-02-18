<?php

declare(strict_types=1);

namespace App\Admin\Form;

use App\Entity\Alternative;
use App\Form\AddressFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlternativeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'alternative',
                'attr' => [
                    'placeholder' => 'Nom de l\'alternative',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('description', TrixType::class, [
                'label' => 'Description de l\'alternative',
                'attr' => [
                    'placeholder' => 'Description de l\'alternative',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => 'Ne surtout pas indiquer l\'adresse de l\'alternative dans la description !',
            ])
            ->add('address', AddressFormType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alternative::class,
        ]);
    }
}
