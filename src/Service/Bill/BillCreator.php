<?php

declare(strict_types=1);

namespace App\Service\Bill;

use App\Entity\Companion;
use App\Entity\Registration;
use App\Entity\User;
use App\Service\MembershipCreator;

final class BillCreator
{
    public function __construct(
        private readonly MembershipCreator $membershipCreator,
    ) {
    }

    public function create(Registration $registration): Bill
    {
        $bill = new Bill();
        $bill->peopleNeedingMembership = $this->membershipCreator->getPeopleNeedingMembershipForRegistration($registration);

        foreach ($registration->getPeople() as $person) {
            $bill->registrations[$person->getFullname()] = $this->getPriceForPerson($person, $registration);
        }

        return $bill;
    }

    public function getPriceForPerson(User|Companion $person, Registration $registration): Price
    {
        if ($person->getAge() < 3) {
            return Price::fixed(0);
        }

        if ($person->getAge() < 13) {
            return Price::fixed(1000 * $registration->payingDaysOfPresence());
        }

        if ($person->getAge() < 18) {
            return Price::fixed(2000 * $registration->payingDaysOfPresence());
        }

        $event = $registration->getEvent();

        $minimumPrice = min($registration->payingDaysOfPresence(), $event->getDaysAtSolidarityPrice()) * $event->getMinimumPricePerDay();
        if ($registration->payingDaysOfPresence() > $event->getDaysAtSolidarityPrice()) {
            $minimumPrice += ($registration->payingDaysOfPresence() - $event->getDaysAtSolidarityPrice()) * $event->getBreakEvenPricePerDay();
        }

        return Price::adjustable(
            $minimumPrice,
            $event->getBreakEvenPricePerDay() * $registration->payingDaysOfPresence(),
            $event->getSupportPricePerDay() * $registration->payingDaysOfPresence(),
        );
    }
}
