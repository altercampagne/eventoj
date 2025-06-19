<?php

declare(strict_types=1);

namespace App\Service\Availability;

use App\Entity\Meal;
use App\Entity\Registration;
use App\Entity\Stage;

final class MealAvailability
{
    public ItemAvailability $adults;

    public ItemAvailability $children;

    public ItemAvailability $bikes;

    public function __construct(
        public readonly Stage $stage,
        public readonly Meal $meal,
        public readonly bool $withPreparers,
    ) {
        // If we have to count preparers (ie: for food calculator), we increase
        // both the item max seats & decrease availability.
        $adultsCapacity = $stage->getEvent()->getAdultsCapacity();
        if ($withPreparers) {
            $adultsCapacity += \count($stage->getPreparers());
        }

        $this->adults = new ItemAvailability($stage, $meal, $adultsCapacity);
        if ($withPreparers) {
            $this->adults->availability -= \count($stage->getPreparers());
        }

        $this->children = new ItemAvailability($stage, $meal, $stage->getEvent()->getChildrenCapacity());
        $this->bikes = new ItemAvailability($stage, $meal, $stage->getEvent()->getBikesAvailable());
    }

    public function isEnoughForRegistration(Registration $registration): bool
    {
        if ($registration->countPeople() - $registration->countChildren() > $this->adults->availability) {
            return false;
        }

        if ($registration->countChildren() > 0 && $registration->countChildren() > $this->children->availability) {
            return false;
        }

        return !($registration->getNeededBike() > 0 && $registration->getNeededBike() > $this->bikes->availability);
    }

    public function getPeopleCount(): int
    {
        return $this->adults->getBooked() + $this->children->getBooked();
    }
}
