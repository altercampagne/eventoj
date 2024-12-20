<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Registration;
use App\Entity\Stage;
use App\Entity\StageRegistration;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Registration>
 */
final class RegistrationFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Registration::class;
    }

    public function withStagesRegistrations(?int $firstStage = null, ?int $lastStage = null): self
    {
        if (null !== $firstStage && null === $lastStage) {
            throw new \InvalidArgumentException('Cannot set $firstStage without $lastStage');
        }
        if (null === $firstStage && null !== $lastStage) {
            throw new \InvalidArgumentException('Cannot set $lastStage without $firstStage');
        }

        return $this->afterInstantiate(static function (Registration $registration) use ($firstStage, $lastStage): void {
            $stages = $registration->getEvent()->getStages()->toArray();

            // If it's an EB, the registration covers all stages! Otherwise, we select random ones
            if (!$registration->getEvent()->isEB()) {
                if (null !== $firstStage) {
                    $stages = \array_slice($stages, $firstStage, $lastStage);
                } elseif (5 > \count($stages)) {
                    throw new \RuntimeException('Not enough stages in event to generate registrations');
                } else {
                    $start = random_int(0, \count($stages) - 5);
                    $stages = \array_slice($stages, $start, $start + 5);
                }
            }

            $stagesRegistrations = array_map(function (Stage $stage) use ($registration): StageRegistration {
                return new StageRegistration(stage: $stage, registration: $registration);
            }, $stages);

            $registration
                ->setStagesRegistrations($stagesRegistrations)
                ->setPrice(self::faker()->numberBetween(10, 70) * 100 * \count($stages))
            ;
        });
    }

    public function confirmed(): self
    {
        return $this->afterInstantiate(static function (Registration $registration): void {
            $registration->confirm();
        });
    }

    protected function defaults(): array|callable
    {
        return [
            'event' => EventFactory::new()->published()->withRandomStages(),
            'user' => UserFactory::new(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (Registration $registration, array $attributes): void {
                if ($registration->getEvent()->isAT()) {
                    /** @var int $neededBiked */
                    $neededBiked = $attributes['neededBike'] ?? self::faker()->numberBetween(0, 2);

                    $registration->setNeededBike($neededBiked);
                }
            })
        ;
    }
}
