<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Companion;
use App\Entity\Diet;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Companion>
 */
final class CompanionFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Companion::class;
    }

    public function children(): self
    {
        return $this->with([
            'birthDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-13 years', 'now')),
        ]);
    }

    public function adult(): self
    {
        return $this->with([
            'birthDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-80 years', '-19 years')),
        ]);
    }

    protected function defaults(): array|callable
    {
        return [
            'birthDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'diet' => self::faker()->randomElement(Diet::cases()),
            'email' => self::faker()->email(),
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'glutenIntolerant' => self::faker()->boolean(),
            'lactoseIntolerant' => self::faker()->boolean(),
            'user' => UserFactory::new(),
        ];
    }
}