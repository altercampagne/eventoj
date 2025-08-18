<?php

declare(strict_types=1);

namespace App\Service\UserEventRegistration;

use App\Entity\Event;
use App\Entity\Meal;
use App\Entity\Registration;
use App\Entity\Stage;
use App\Entity\StageRegistration;
use App\Entity\User;

final readonly class UserEventComputedRegistrations
{
    /**
     * @var StageRegistration[]
     */
    private array $stagesRegistrations;

    public function __construct(User $user, Event $event)
    {
        $registrations = $user->getRegistrations()->filter(fn (Registration $registration): bool => $registration->getEvent() === $event && $registration->isConfirmed());

        $stagesRegistrations = [];
        foreach ($registrations as $registration) {
            $stagesRegistrations = array_merge($stagesRegistrations, $registration->getStagesRegistrations()->toArray());
        }

        $this->stagesRegistrations = $stagesRegistrations;
    }

    public function hasRegistrationForStage(Stage $stage): bool
    {
        return [] !== $this->filterStagesRegistrations($stage);
    }

    public function hasRegistrationForStageAndMeal(Stage $stage, Meal $meal): bool
    {
        return [] !== $this->filterStagesRegistrations($stage, $meal);
    }

    public function hasRegistrationForAllMealsOfStage(Stage $stage): bool
    {
        foreach (Meal::cases() as $meal) {
            if ([] === $this->filterStagesRegistrations($stage, $meal)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return StageRegistration[]
     */
    private function filterStagesRegistrations(Stage $stage, ?Meal $meal = null): array
    {
        $relevantStagesRegistrations = [];
        foreach ($this->stagesRegistrations as $stageRegistration) {
            if ($stageRegistration->getStage() !== $stage) {
                continue;
            }

            if (null !== $meal && !$stageRegistration->includesMeal($meal)) {
                continue;
            }

            $relevantStagesRegistrations[] = $stageRegistration;
        }

        return $relevantStagesRegistrations;
    }
}
