<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Diet;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
                'required' => false, // This information is required but the HTML5 validation if perfectible on mobile devices (Android only?)
                'label' => 'Ton régime alimentaire',
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
                'label' => 'Autre (intolérance, allergie, ...)',
                'attr' => [
                    'placeholder' => 'Autre (intolérance, allergie, ...)',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => '<i class="ms-2 me-1 fa-solid fa-hand-point-up"></i> Laisser vide s\'il n\'y a rien d\'autre à préciser.',
                'help_html' => true,
                'required' => false,
            ])
            ->add('visibleOnAlterpotesMap', CheckboxType::class, [
                'label' => "J'accepte d'apparaitre sur la carte des alterpotes",
                'help' => 'La carte est visible uniquement pour les membres à jour de leur cotisation.',
                'required' => false,
            ])
            ->add('biography', TextareaType::class, [
                'label' => 'Ta présentation (visible sur la carte)',
                'help' => 'N\'hésite pas à ajouter des informations utiles pour les autres membres de l\'association : est-ce que tu acceptes d\'héberger des altercyclistes ? dans quel contexte ?',
                'required' => false,
            ])
        ;

        if ($user->isAdult()) {
            $builder->add('hasDrivingLicence', CheckboxType::class, [
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => 'profile_update',
        ]);
    }
}
