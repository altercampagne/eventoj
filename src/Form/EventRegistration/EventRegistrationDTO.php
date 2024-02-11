<?php

declare(strict_types=1);

namespace App\Form\EventRegistration;

use App\Entity\Companion;
use App\Entity\Meal;
use App\Entity\Registration;
use App\Entity\Stage;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EventRegistrationDTO
{
    private readonly Registration $registration;

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
    #[Assert\NotBlank(groups: ['choose_dates'])]
    #[Assert\Range(min: 20, minMessage: 'Le prix minimum par jour est de {{ limit }} €.', groups: ['choose_dates'])]
    public int $pricePerDay = 33;

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
            $this->stageStart = $stage = $stageRegistration->getStage();
            $this->firstMeal = $stageRegistration->getFirstMeal();
        } else {
            if (null === $stageStart = $registration->getEvent()->getNextComingStage()) {
                throw new \RuntimeException('Cannot register to an event without coming stages');
            }

            $endDate = $stageStart->getDate()->modify('+4 days');
            $stageEnd = $registration->getEvent()->getStages()->findFirst(static function (int $key, Stage $stage) use ($endDate): bool {
                return $stage->getDate() >= $endDate;
            });

            $this->stageEnd = $stageEnd ?: $registration->getEvent()->getStages()->last() ?: throw new \RuntimeException('Looks like it is not possible to determine an end date.');
        }

        if (false !== $stageRegistration = $registration->getStagesRegistrations()->last()) {
            $this->stageEnd = $stage = $stageRegistration->getStage();
            $this->lastMeal = $stageRegistration->getLastMeal();
        }

        $this->companions = $registration->getCompanions();
        $this->neededBike = $registration->getNeededBike();
        $this->pricePerDay = $registration->getPricePerDay() / 100;
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

        $numberOfDays = (int) $this->stageStart->getDate()->diff($this->stageEnd->getDate())->format('%a');
        if ($numberOfDays < 1) {
            $context->buildViolation('Tu dois rester au minimum une journée sur l\'évènement.')
                ->addViolation();
        }

        foreach ($this->getBookedStages() as $stage) {
            $availability = $stage->getAvailability();
            if ($availability->children < $this->registration->countChildren()) {
                $context->buildViolation('Il n\'y a pas assez de disponibilités sur la période sélectionnée.')
                        ->addViolation();

                return;
            }
            if ($availability->adults < $this->registration->countPeople() - $this->registration->countChildren()) {
                $context->buildViolation('Il n\'y a pas assez de disponibilités sur la période sélectionnée.')
                        ->addViolation();

                return;
            }
            if ($availability->bikes < $this->registration->getNeededBike()) {
                $context->buildViolation('Il n\'y a pas assez de vélos disponibles sur la période sélectionnée.')
                        ->addViolation();

                return;
            }
        }
    }
}
