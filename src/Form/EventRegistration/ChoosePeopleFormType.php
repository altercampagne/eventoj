<?php

declare(strict_types=1);

namespace App\Form\EventRegistration;

use App\Entity\Companion;
use App\Entity\Registration;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoosePeopleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Registration $registration */
        $registration = $options['registration'];

        $builder
            ->add('companions', EntityType::class, [
                'class' => Companion::class,
                'required' => false,
                'label' => 'Tes compagnon·es',
                'choice_label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => $registration->getUser()->getCompanions(),
                'choice_attr' => static function (Companion $companion): array {
                    return [
                        'data-fullname' => $companion->getFullName(),
                        'data-birthdate' => $companion->getBirthDate()->format('d/m/Y'),
                    ];
                },
            ])
        ;

        if ($registration->getEvent()->isAT()) {
            $builder->add('neededBike', IntegerType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('registration');
        $resolver->setAllowedTypes('registration', Registration::class);

        $resolver->setDefaults([
            'data_class' => EventRegistrationDTO::class,
            'validation_groups' => 'choose_people',
        ]);
    }
}
