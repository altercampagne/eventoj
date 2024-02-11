<?php

declare(strict_types=1);

namespace App\Service\PriceCalculation;

use App\Entity\Registration;

final class RegistrationPriceCalculator
{
    public function calculate(Registration $registration): Bill
    {
        $bill = new Bill();

        if (1 === $registration->getNeededBike()) {
            $bill->addLine('Vélo de prêt', 0);
        } elseif (1 < $registration->getNeededBike()) {
            $bill->addLine("{$registration->getNeededBike()} vélos de prêt", 0);
        }

        $nbPersons = $registration->countPeople();
        $nbChilds = $registration->countChildren();
        $nbAdults = $nbPersons - $nbChilds;

        if (0 < $nbAdults) {
            if (1 === $nbAdults) {
                $bill->addLine('Adhésion adulte', 1000);
            } else {
                $bill->addLine("$nbAdults adhésions adulte", 1000 * $nbAdults);
            }
        }

        if (0 < $nbChilds) {
            if (1 === $nbChilds) {
                $bill->addLine('Adhésion - de 13 ans', 500);
            } else {
                $bill->addLine("$nbChilds adhésions - de 13 ans", 500 * $nbChilds);
            }
        }

        $registrationPrice = $registration->countDaysOfPresence() * $registration->getPricePerDay();

        if (1 === $nbPersons) {
            $bill->addLine('Inscription à l\'évènement', $registrationPrice);
        } else {
            $bill->addLine("$nbPersons inscriptions à l'évènement", $registrationPrice * $nbPersons);
        }

        return $bill;
    }
}
