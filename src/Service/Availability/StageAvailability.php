<?php

declare(strict_types=1);

namespace App\Service\Availability;

use App\Entity\Meal;
use App\Entity\Stage;

final readonly class StageAvailability
{
    public MealAvailability $breakfast;

    public MealAvailability $lunch;

    public MealAvailability $dinner;

    public function __construct(
        private Stage $stage,
    ) {
        $this->breakfast = new MealAvailability($stage, Meal::BREAKFAST);
        $this->lunch = new MealAvailability($stage, Meal::LUNCH);
        $this->dinner = new MealAvailability($stage, Meal::DINNER);

        foreach ($stage->getConfirmedStagesRegistrations() as $stageRegistration) {
            $registration = $stageRegistration->getRegistration();

            $children = $registration->countChildren();
            $adults = $registration->countPeople() - $children;

            if ($stageRegistration->presentForBreakfast()) {
                $this->breakfast->adults->availability -= $adults;
                $this->breakfast->children->availability -= $children;
                $this->breakfast->bikes->availability -= $registration->getNeededBike();
            }

            if ($stageRegistration->presentForLunch()) {
                $this->lunch->adults->availability -= $adults;
                $this->lunch->children->availability -= $children;
                $this->lunch->bikes->availability -= $registration->getNeededBike();
            }

            if ($stageRegistration->presentForDinner()) {
                $this->dinner->adults->availability -= $adults;
                $this->dinner->children->availability -= $children;
                $this->dinner->bikes->availability -= $registration->getNeededBike();
            }
        }
    }

    /**
     * @return MealAvailability[]
     */
    public function getMealAvailabilities(): array
    {
        return [$this->breakfast, $this->lunch, $this->dinner];
    }

    public function get(Meal $meal): MealAvailability
    {
        return match ($meal) {
            Meal::BREAKFAST => $this->breakfast,
            Meal::LUNCH => $this->lunch,
            Meal::DINNER => $this->dinner,
        };
    }

    public function hasAvailability(): bool
    {
        foreach ($this->getMealAvailabilities() as $mealAvailability) {
            if (!$this->stage->includesMeal($mealAvailability->meal)) {
                continue;
            }

            if ($mealAvailability->adults->availability > 0) {
                return true;
            }
        }

        return false;
    }
}
