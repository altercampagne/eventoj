<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Membership;
use Zenstruck\Foundry\Object\Instantiator;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Membership>
 */
final class MembershipFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Membership::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'user' => UserFactory::new(),
            'payment' => PaymentFactory::new()->approved(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->instantiateWith(Instantiator::namedConstructor('createForUser'))
        ;
    }
}
