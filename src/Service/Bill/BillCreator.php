<?php

declare(strict_types=1);

namespace App\Service\Bill;

use App\Entity\Companion;
use App\Entity\Registration;
use App\Entity\User;
use App\Service\MembershipCreator;

final readonly class BillCreator
{
    public function __construct(
        private MembershipCreator $membershipCreator,
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
        if (null === $firstStageRegistration = $registration->getStageRegistrationStart()) {
            throw new \RuntimeException('Given registration does not contains any stage!');
        }

        // We check the age of the person at the beginning of the registration
        $age = $person->getAge($firstStageRegistration->getStage()->getDate());

        $days = $registration->payingDaysOfPresence();

        if ($age < 3) {
            return Price::fixed(0, $days);
        }

        if ($age < 13) {
            return Price::fixed(1000 * $days, $days);
        }

        if ($age < 18) {
            return Price::fixed(2000 * $days, $days);
        }

        $event = $registration->getEvent();

        $minimumPrice = min($days, $event->getDaysAtSolidarityPrice()) * $event->getMinimumPricePerDay();
        if ($days > $event->getDaysAtSolidarityPrice()) {
            $minimumPrice += ($days - $event->getDaysAtSolidarityPrice()) * $event->getBreakEvenPricePerDay();
        }

        return Price::adjustable(
            $minimumPrice,
            $event->getBreakEvenPricePerDay() * $days,
            $event->getSupportPricePerDay() * $days,
            $days,
        );
    }
}
