<?php

declare(strict_types=1);

namespace App\Form\EventRegistration;

use App\Entity\Registration;
use App\Entity\Stage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                $stages = $options['registration']->getEvent()->getStages()->filter(static function (Stage $stage): bool {
                    return !$stage->isOver();
                });
                if (0 === \count($stages)) {
                    throw new \LogicException('All stages are over, this is not supposed to happen here!');
                }

                if ('start' === $options['when']) {
                    return $stages->slice(0, \count($stages) - 1);
                }

                return $stages->slice(1, \count($stages) - 1);
            },
            'choice_label' => static function (Stage $stage): string {
                return $stage->getDate()->format('d/m').' - '.$stage->getName();
            },
            'choice_attr' => static function (Stage $stage): array {
                $availability = $stage->getAvailability();

                return [
                    'data-availability-adults' => $availability->adults,
                    'data-availability-children' => $availability->children,
                    'data-availability-bikes' => $availability->bikes,
                ];
            },
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
