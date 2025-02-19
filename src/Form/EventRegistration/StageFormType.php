<?php

declare(strict_types=1);

namespace App\Form\EventRegistration;

use App\Entity\Registration;
use App\Entity\Stage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Stage>
 */
class StageFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('registration');
        $resolver->setAllowedTypes('registration', Registration::class);
        $resolver->setRequired('when');
        $resolver->setAllowedValues('when', ['start', 'end']);

        $resolver->setDefaults([
            'class' => Stage::class,
            'choices' => static function (Options $options): iterable {
                /** @var Registration $registration */
                $registration = $options['registration'];

                return $registration->getEvent()->getStages()->filter(static fn (Stage $stage): bool => !$stage->isOver());
            },
            'choice_label' => static fn (Stage $stage): string => $stage->getDate()->format('d/m').' - '.$stage->getName(),
        ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return EntityType::class;
    }
}
