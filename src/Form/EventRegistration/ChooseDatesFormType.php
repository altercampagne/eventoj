<?php

declare(strict_types=1);

namespace App\Form\EventRegistration;

use App\Entity\Meal;
use App\Entity\Registration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChooseDatesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Registration $registration */
        $registration = $options['registration'];

        $builder
            ->add('stageStart', StageFormType::class, [
                'label' => 'L\'étape où tu nous rejoins',
                'event' => $registration->getEvent(),
                'attr' => [
                    'class' => 'form-control-lg',
                ],
            ])
            ->add('firstMeal', EnumType::class, [
                'label' => 'Le premier repas que tu partageras avec nous',
                'class' => Meal::class,
                'attr' => [
                    'class' => 'form-control-lg',
                ],
            ])
            ->add('stageEnd', StageFormType::class, [
                'label' => 'L\'étape où tu nous quittes',
                'event' => $registration->getEvent(),
                'attr' => [
                    'class' => 'form-control-lg',
                ],
            ])
            ->add('lastMeal', EnumType::class, [
                'label' => 'Le dernier repas avant ton départ',
                'class' => Meal::class,
                'attr' => [
                    'class' => 'form-control-lg',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('registration');
        $resolver->setAllowedTypes('registration', Registration::class);

        $resolver->setDefaults([
            'data_class' => EventRegistrationDTO::class,
            'validation_groups' => 'choose_dates',
        ]);
    }
}
