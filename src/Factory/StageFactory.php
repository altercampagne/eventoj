<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Stage;
use App\Entity\StageType;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Stage>
 */
final class StageFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Stage::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'event' => EventFactory::new(),
            'type' => StageType::CLASSIC,
            'name' => self::faker()->text(),
            'description' => self::faker()->text(),
            'date' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(static function (Stage $stage): void {
                $stage->getEvent()->addStage($stage);
            })
        ;
    }
}
