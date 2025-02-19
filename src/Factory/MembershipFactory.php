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
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'user' => UserFactory::new(),
            'payment' => PaymentFactory::new()->approved(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->instantiateWith(Instantiator::namedConstructor('createForUser'))
        ;
    }
}
