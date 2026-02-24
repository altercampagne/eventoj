<?php

declare(strict_types=1);

namespace App\Admin\Form\RegistrationCreate;

use App\Admin\Form\UserAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<ChooseUserFormDTO>
 */
class ChooseUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserAutocompleteField::class, [
                'label' => 'Personne à inscrire',
                'required' => true,
                'multiple' => false,
                'attr' => [
                    'placeholder' => 'Rechercher une personne',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChooseUserFormDTO::class,
        ]);
    }
}
