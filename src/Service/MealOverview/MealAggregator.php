<?php

declare(strict_types=1);

namespace App\Service\MealOverview;

use App\Entity\Companion;
use App\Entity\Diet;
use App\Entity\Event;
use App\Entity\Meal;
use App\Entity\User;

final class MealAggregator
{
    /* @phpstan-ignore missingType.iterableValue */
    public function aggregate(Event $event): array
    {
        $overview = [];
        foreach ($event->getStages() as $stage) {
            $name = $stage->getDate()->format('d/m').' - '.$stage->getName();

            $overview[$name] = [
                'stage' => $stage,
                'meals' => [],
            ];

            foreach (Meal::cases() as $meal) {
                $overview[$name]['meals'][$meal->value] = [
                    'meal' => $meal,
                    'total' => 0,
                    Diet::OMNIVORE->value => 0,
                    Diet::VEGETARIAN->value => 0,
                    Diet::VEGAN->value => 0,
                    'lactoseIntolerant' => 0,
                    'glutenIntolerant' => 0,
                    'dietDetails' => [],
                ];
            }

            foreach ($stage->getPreparers() as $preparer) {
                foreach (Meal::cases() as $meal) {
                    $this->addPersonForMeal($preparer, $name, $meal, $overview);
                }
            }

            foreach ($stage->getConfirmedStagesRegistrations() as $stageRegistration) {
                $people = $stageRegistration->getRegistration()->getPeople();

                foreach (Meal::cases() as $meal) {
                    if (!$stageRegistration->includesMeal($meal)) {
                        continue;
                    }

                    foreach ($people as $person) {
                        $this->addPersonForMeal($person, $name, $meal, $overview);
                    }
                }
            }
        }

        return $overview;
    }

    private function addPersonForMeal(User|Companion $person, string $stageName, Meal $meal, array &$overview): void
    {
        ++$overview[$stageName]['meals'][$meal->value]['total'];
        if (null === $person->getDiet()) {
            return;
        }

        ++$overview[$stageName]['meals'][$meal->value][$person->getDiet()->value];

        if ($person->isLactoseIntolerant()) {
            ++$overview[$stageName]['meals'][$meal->value]['lactoseIntolerant'];
        }
        if ($person->isGlutenIntolerant()) {
            ++$overview[$stageName]['meals'][$meal->value]['glutenIntolerant'];
        }
        if (null !== $details = $person->getDietDetails()) {
            $overview[$stageName]['meals'][$meal->value]['dietDetails'][$person->getPublicName()] = $details;
        }
    }
}
