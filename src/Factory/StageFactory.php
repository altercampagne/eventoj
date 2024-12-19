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
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Stage::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'event' => EventFactory::new(),
            'type' => StageType::CLASSIC,
            'name' => self::faker()->text(),
            'description' => self::faker()->text(),
            'date' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(static function (Stage $stage): void {
                $stage->getEvent()->addStage($stage);
            })
        ;
    }
}
