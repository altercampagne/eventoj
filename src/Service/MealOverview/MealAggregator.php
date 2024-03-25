<?php

declare(strict_types=1);

namespace App\Service\MealOverview;

use App\Entity\Diet;
use App\Entity\Event;
use App\Entity\Meal;

final class MealAggregator
{
    /* @phpstan-ignore-next-line */
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

            foreach ($stage->getStagesRegistrations() as $stageRegistration) {
                $people = $stageRegistration->getRegistration()->getPeople();

                foreach (Meal::cases() as $meal) {
                    if (!$stageRegistration->includesMeal($meal)) {
                        continue;
                    }

                    $overview[$name]['meals'][$meal->value]['total'] += \count($people);

                    foreach ($people as $person) {
                        if (null === $person->getDiet()) {
                            continue;
                        }
                        ++$overview[$name]['meals'][$meal->value][$person->getDiet()->value];
                        if ($person->isLactoseIntolerant()) {
                            ++$overview[$name]['meals'][$meal->value]['lactoseIntolerant'];
                        }
                        if ($person->isGlutenIntolerant()) {
                            ++$overview[$name]['meals'][$meal->value]['glutenIntolerant'];
                        }
                        if (null !== $details = $person->getDietDetails()) {
                            $overview[$name]['meals'][$meal->value]['dietDetails'][$person->getPublicName()] = $details;
                        }
                    }
                }
            }
        }

        return $overview;
    }
}
