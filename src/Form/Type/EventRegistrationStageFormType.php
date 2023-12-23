<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Event;
use App\Entity\Stage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventRegistrationStageFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('event');
        $resolver->setAllowedTypes('event', Event::class);

        $resolver->setDefaults([
            'class' => Stage::class,
            'choices' => static function (Options $options): iterable {
                return $options['event']->getStages();
            },
            'choice_label' => static function (Stage $stage): string {
                return $stage->getDate()->format('d/m').' - '.$stage->getName();
            },
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
