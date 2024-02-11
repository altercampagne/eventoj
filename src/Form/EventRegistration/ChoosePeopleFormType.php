<?php

declare(strict_types=1);

namespace App\Form\EventRegistration;

use App\Entity\Companion;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoosePeopleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $options['user'];

        $builder
            ->add('companions', EntityType::class, [
                'class' => Companion::class,
                'required' => false,
                'label' => 'Tes compagnon·es',
                'choice_label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => $user->getCompanions(),
                'choice_attr' => static function (Companion $companion): array {
                    return [
                        'data-fullname' => $companion->getFullName(),
                        'data-birthdate' => $companion->getBirthDate()->format('d/m/Y'),
                    ];
                },
            ])
        ;

        $builder->add('neededBike', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', User::class);

        $resolver->setDefaults([
            'data_class' => EventRegistrationDTO::class,
            'validation_groups' => 'choose_people',
        ]);
    }
}
