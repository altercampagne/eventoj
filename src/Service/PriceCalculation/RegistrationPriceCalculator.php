<?php

declare(strict_types=1);

namespace App\Service\PriceCalculation;

use App\Entity\Registration;
use App\Entity\User;

final class RegistrationPriceCalculator
{
    public function calculate(Registration $registration): Bill
    {
        $bill = new Bill();

        if ($registration->needBike()) {
            $bill->addLine('Vélo de prêt', 0);
        }

        if ($this->needMembership($registration->getUser())) {
            $bill->addLine('Adhésion à l\'association', 1000);
        }

        $bill->addLine('Inscription à l\'évènement', $registration->countDaysOfPresence() * $registration->getPricePerDay());

        return $bill;
    }

    private function needMembership(User $user): bool
    {
        return true;
    }
}
