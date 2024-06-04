<?php

declare(strict_types=1);

namespace App\Form\EventRegistration;

use App\Entity\Companion;
use App\Entity\Meal;
use App\Entity\Registration;
use App\Entity\Stage;
use App\Validator\DoesNotOverlapWithAnotherRegistration;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[DoesNotOverlapWithAnotherRegistration(groups: ['choose_dates'])]
class EventRegistrationDTO
{
    public readonly Registration $registration;

    #[Assert\NotBlank(groups: ['choose_dates'])]
    public ?Stage $stageStart = null;
    #[Assert\NotBlank(groups: ['choose_dates'])]
    public Meal $firstMeal = Meal::DINNER;
    #[Assert\NotBlank(groups: ['choose_dates'])]
    public ?Stage $stageEnd = null;
    #[Assert\NotBlank(groups: ['choose_dates'])]
    public Meal $lastMeal = Meal::BREAKFAST;
    #[Assert\NotNull(groups: ['choose_dates'])]
    public int $neededBike = 0;

    /**
     * @var Collection<int, Companion>
     */
    public Collection $companions;

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;

        if (0 == \count($registration->getEvent()->getStages())) {
            throw new \RuntimeException('Cannot register to an event without stages.');
        }

        if (false !== $stageRegistration = $registration->getStagesRegistrations()->first()) {
            $this->stageStart = $stageRegistration->getStage();
            $this->firstMeal = $stageRegistration->getFirstMeal();
        } else {
            if (null === $stageStart = $registration->getEvent()->getNextComingStage()) {
                throw new \RuntimeException('Cannot register to an event without coming stages');
            }
            $this->stageStart = $stageStart;

            $endDate = $stageStart->getDate()->modify('+4 days');
            $stageEnd = $registration->getEvent()->getStages()->findFirst(static function (int $key, Stage $stage) use ($endDate): bool {
                return $stage->getDate() >= $endDate;
            });

            $this->stageEnd = $stageEnd ?: $registration->getEvent()->getStages()->last() ?: throw new \RuntimeException('Looks like it is not possible to determine an end date.');
        }

        if (false !== $stageRegistration = $registration->getStagesRegistrations()->last()) {
            $this->stageEnd = $stageRegistration->getStage();
            $this->lastMeal = $stageRegistration->getLastMeal();
        }

        $this->companions = $registration->getCompanions();
        $this->neededBike = $registration->getNeededBike();
    }

    /**
     * @return Stage[]
     */
    public function getBookedStages(): array
    {
        $stages = $this->registration->getEvent()->getStages()->toArray();

        $startIndex = (int) array_search($this->stageStart, $stages, true);

        return \array_slice(
            $stages,
            $startIndex,
            (int) array_search($this->stageEnd, $stages, true) - $startIndex + 1,
        );
    }

    /**
     * @return Meal[]
     */
    public function getFirstDayMeals(): array
    {
        if (1 < \count($this->getBookedStages())) {
            return $this->firstMeal->getFollowingMeals(includeSelf: true);
        }

        return array_uintersect(
            $this->firstMeal->getFollowingMeals(includeSelf: true),
            $this->lastMeal->getPreviousMeals(includeSelf: true),
            static function ($meal1, $meal2): int {
                /* @phpstan-ignore-next-line */
                return $meal1->compare($meal2);
            }
        );
    }

    /**
     * @return Meal[]
     */
    public function getLastDayMeals(): array
    {
        if (1 < \count($this->getBookedStages())) {
            return $this->lastMeal->getPreviousMeals(includeSelf: true);
        }

        return array_uintersect(
            $this->firstMeal->getFollowingMeals(includeSelf: true),
            $this->lastMeal->getPreviousMeals(includeSelf: true),
            static function ($meal1, $meal2): int {
                /* @phpstan-ignore-next-line */
                return $meal1->compare($meal2);
            }
        );
    }

    #[Assert\Callback(groups: ['choose_people'])]
    public function validateNeededBike(ExecutionContextInterface $context, mixed $payload): void
    {
        if ($this->neededBike > \count($this->companions) + 1) {
            $context->buildViolation('Plus d\'un vélo par personne, ça fait beaucoup ! 🤯')
                ->atPath('neededBike')
                ->addViolation();
        }
    }

    #[Assert\Callback(groups: ['choose_dates'])]
    public function validatePeriod(ExecutionContextInterface $context, mixed $payload): void
    {
        if (null === $this->stageStart || null === $this->stageEnd) {
            return;
        }

        $now = new \DateTimeImmutable();
        if ($this->stageStart->getDate() < $now) {
            $context->buildViolation('Cette date d\'arrivée est dans le passé.')
                ->atPath('stageStart')
                ->addViolation();
        }
        if ($this->stageEnd->getDate() < $now) {
            $context->buildViolation('Cette date de départ est dans le passé.')
                ->atPath('stageEnd')
                ->addViolation();
        }

        if ($this->stageEnd->getDate() < $this->stageStart->getDate()) {
            $context->buildViolation('Tu ne peux pas repartir avant même d\'être arrivé.')
                ->addViolation();
        }

        if ($this->stageStart === $this->stageEnd) {
            if (Meal::LUNCH === $this->firstMeal && Meal::BREAKFAST === $this->lastMeal) {
                $context->buildViolation('Tu ne peux pas repartir avant même d\'être arrivé.')
                    ->addViolation();
            } elseif (Meal::DINNER === $this->firstMeal && Meal::DINNER !== $this->lastMeal) {
                $context->buildViolation('Tu ne peux pas repartir avant même d\'être arrivé.')
                    ->addViolation();
            }
        }
    }

    #[Assert\Callback(groups: ['choose_dates'])]
    public function validateAvailability(ExecutionContextInterface $context, mixed $payload): void
    {
        foreach ($this->getBookedStages() as $bookedStage) {
            if ($this->stageStart === $bookedStage) {
                $meals = $this->getFirstDayMeals();
            } elseif ($this->stageEnd === $bookedStage) {
                $meals = $this->getLastDayMeals();
            } else {
                $meals = Meal::cases();
            }

            foreach ($bookedStage->getAvailability()->getMealAvailabilities() as $mealAvailability) {
                if (!in_array($mealAvailability->meal, $meals)) {
                    continue;
                }

                if (!$mealAvailability->isEnoughForRegistration($this->registration)) {
                    $context->buildViolation('Il n\'y a pas assez de disponibilités sur la période sélectionnée.')
                            ->addViolation();

                    return;
                }
            }
        }
    }
}
