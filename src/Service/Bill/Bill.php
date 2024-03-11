<?php

declare(strict_types=1);

namespace App\Service\Bill;

use App\Entity\Companion;
use App\Entity\Membership;
use App\Entity\User;

final class Bill
{
    /**
     * @var array<User|Companion>
     */
    public array $peopleNeedingMembership = [];

    /**
     * @var array<string, Price>
     */
    public array $registrations = [];

    public function getMinimumPrice(): int
    {
        $price = \count($this->peopleNeedingMembership) * Membership::PRICE;

        foreach ($this->registrations as $registrationPrice) {
            $price += $registrationPrice->minimumAmount;
        }

        return $price;
    }

    public function getBreakEvenPrice(): int
    {
        $price = \count($this->peopleNeedingMembership) * Membership::PRICE;

        foreach ($this->registrations as $registrationPrice) {
            $price += $registrationPrice->breakEvenAmount;
        }

        return $price;
    }

    public function getSupportPrice(): int
    {
        $price = \count($this->peopleNeedingMembership) * Membership::PRICE;

        foreach ($this->registrations as $registrationPrice) {
            $price += $registrationPrice->supportAmount;
        }

        return $price;
    }
}
