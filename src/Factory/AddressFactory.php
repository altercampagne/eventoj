<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Address;
use Zenstruck\Foundry\ObjectFactory;

/**
 * @extends ObjectFactory<Address>
 */
final class AddressFactory extends ObjectFactory
{
    public static function class(): string
    {
        return Address::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'addressLine1' => self::faker()->optional()->streetAddress(),
            'addressLine2' => self::faker()->optional(0.2)->streetAddress(),
            'city' => self::faker()->city(),
            'zipCode' => self::faker()->postCode(),
            'countryCode' => 'FR',
        ];
    }
}
