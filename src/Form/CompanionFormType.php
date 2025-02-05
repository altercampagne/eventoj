<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Companion;
use App\Entity\Diet;
use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Companion>
 */
class CompanionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Son prénom',
                'attr' => [
                    'placeholder' => 'Son prénom',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Son nom',
                'attr' => [
                    'placeholder' => 'Son nom',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('birthDate', BirthdayType::class, [
                'required' => true,
                'widget' => 'single_text',
                'label' => 'Sa date de naissance',
                'attr' => [
                    'placeholder' => 'Sa date de naissance',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'input' => 'datetime_immutable',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Son adresse mail',
                'attr' => [
                    'placeholder' => 'Son adresse mail',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('phoneNumber', PhoneNumberType::class, [
                'required' => false,
                'format' => PhoneNumberFormat::NATIONAL,
                'default_region' => 'FR',
                'label' => 'Son numéro de téléphone',
                'attr' => [
                    'placeholder' => 'Son numéro de téléphone',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('diet', EnumType::class, [
                'label' => 'Son régime alimentaire',
                'class' => Diet::class,
                'placeholder' => 'Choix du régime alimentaire',
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
            'data_class' => Companion::class,
        ]);
    }
}
