<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Payment;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Payment>
 */
final class PaymentFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Payment::class;
    }

    public function withInstalments(int $instalments = 3): self
    {
        return $this->with([
            'instalments' => 3,
        ]);
    }

    public function approved(): self
    {
        return $this
            ->afterInstantiate(static function (Payment $payment): void {
                $payment->approve(new \DateTimeImmutable());
            })
        ;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'payer' => UserFactory::new(),
            'amount' => self::faker()->numberBetween(10, 70) * 100,
        ];
    }
}
