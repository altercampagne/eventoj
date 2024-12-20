<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Event;
use App\Entity\Meal;
use Zenstruck\Foundry\Object\Instantiator;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Event>
 */
final class EventFactory extends PersistentProxyObjectFactory
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
        return Event::class;
    }

    public function AT(): static
    {
        return $this
            ->instantiateWith(Instantiator::namedConstructor('AT'))
        ;
    }

    public function EB(): static
    {
        return $this
            ->instantiateWith(Instantiator::namedConstructor('EB'))
        ;
    }

    public function published(?\DateTimeImmutable $publishedAt = null): static
    {
        return $this->with(['publishedAt' => $publishedAt ?? new \DateTimeImmutable()]);
    }

    public function withRandomStages(string $date = 'first day of July', int $count = 31): self
    {
        $date = new \DateTimeImmutable($date);
        if ($date < new \DateTimeImmutable()) {
            $date = $date->modify('+1 year');
        }

        return $this
            ->afterPersist(static function (Event $event) use ($date, $count): void {
                StageFactory::createSequence(static function () use ($event, $date, $count): iterable {
                    for ($i = 1; $i <= $count; ++$i) {
                        yield [
                            'event' => $event,
                            'name' => "Day #$i",
                            'description' => "Jour #$i",
                            'date' => $date,
                        ];

                        $date = $date->modify('+1 day');
                    }
                });
            })
        ;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->words(3, true),
            'description' => self::faker()->sentence(),
            'firstMealOfFirstDay' => self::faker()->randomElement(Meal::cases()),
            'lastMealOfLastDay' => self::faker()->randomElement(Meal::cases()),
            'openingDateForBookings' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'picture' => UploadedFileFactory::new(),
            'adultsCapacity' => 10,
            'childrenCapacity' => 5,
            'bikesAvailable' => 5,
            'pahekoProjectId' => 1,
            'exchangeMarketLink' => 'https://localhost',
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->instantiateWith(Instantiator::namedConstructor('AT'))
        ;
    }
}
