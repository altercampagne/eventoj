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

class ContactDetailsUpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $options['data'];

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Ton adresse mail',
                'attr' => [
                    'placeholder' => 'Ton adresse mail',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => $user->isVerified() ? '<i class="ms-2 me-1 fa-solid fa-triangle-exclamation"></i> En cas de changement, ton adresse mail devra de nouveau être validée.' : null,
                'help_html' => $user->isVerified(),
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
            ->add('publicName', TextType::class, [
                'required' => false,
                'label' => 'Ton nom d\'affichage',
                'attr' => [
                    'placeholder' => 'Ton nom d\'affichage',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => 'C\'est le nom qui sera visible publiquement, n\'hésite pas à utiliser ton petit surnom !',
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
