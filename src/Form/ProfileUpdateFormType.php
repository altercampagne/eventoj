<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Diet;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileUpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $options['data'];

        $builder
            ->add('diet', EnumType::class, [
                'label' => 'Ton régime alimentaire',
                'class' => Diet::class,
            ])
            ->add('glutenIntolerant', CheckboxType::class, [
                'label' => 'Intolérance au gluten',
                'required' => false,
            ])
            ->add('lactoseIntolerant', CheckboxType::class, [
                'label' => 'Intolérance au lactose',
                'required' => false,
            ])
            ->add('dietDetails', TextType::class, [
                'label' => 'Autre spécificités',
                'attr' => [
                    'placeholder' => 'Autre spécificités',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => '<i class="ms-2 me-1 fa-solid fa-hand-point-up"></i> Laisser vide s\'il n\'y a rien d\'autre à préciser.',
                'help_html' => true,
                'required' => false,
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
