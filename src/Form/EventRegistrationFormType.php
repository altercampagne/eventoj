<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Event;
use App\Entity\Meal;
use App\Form\Type\EventRegistrationStageFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Event $event */
        $event = $options['event'];

        $builder
            ->add('stageStart', EventRegistrationStageFormType::class, [
                'label' => 'L\'étape où tu nous rejoins',
                'event' => $event,
            ])
            ->add('firstMeal', EnumType::class, [
                'label' => 'Le premier repas que tu partageras avec nous',
                'class' => Meal::class,
            ])
            ->add('stageEnd', EventRegistrationStageFormType::class, [
                'label' => 'L\'étape où tu nous quittes',
                'event' => $event,
            ])
            ->add('lastMeal', EnumType::class, [
                'label' => 'Le dernier repas avant ton départ',
                'class' => Meal::class,
            ])
            ->add('needBike', CheckboxType::class)
            ->add('pricePerDay', IntegerType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('event');
        $resolver->setAllowedTypes('event', Event::class);

        $resolver->setDefaults([
            'data_class' => EventRegistrationDTO::class,
        ]);
    }
}
