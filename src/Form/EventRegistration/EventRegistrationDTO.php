<?php

declare(strict_types=1);

namespace App\Form\EventRegistration;

use App\Entity\Companion;
use App\Entity\Event;
use App\Entity\Meal;
use App\Entity\Registration;
use App\Entity\Stage;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EventRegistrationDTO
{
    public Event $event;

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

    public function __construct(Event $event, ?Registration $registration = null)
    {
        $this->event = $event;

        $stages = $this->event->getStages();

        if (0 == \count($stages)) {
            throw new \RuntimeException('Cannot register to an event without stages.');
        }

        $now = new \DateTimeImmutable();
        if (null === $stageStart = $stages->findFirst(static function (int $key, Stage $stage) use ($now): bool {
            return $stage->getDate() > $now;
        })) {
            throw new \RuntimeException('Cannot register to a past event.');
        }

        $this->stageStart = $stageStart;

        $endDate = $stageStart->getDate()->modify('+4 days');
        $stageEnd = $stages->findFirst(static function (int $key, Stage $stage) use ($endDate): bool {
            return $stage->getDate() >= $endDate;
        });

        $this->stageEnd = $stageEnd ?: $stages->last() ?: throw new \RuntimeException('Looks like it is not possible to determine an end date.');

        if (null === $registration) {
            return;
        }

        $this->companions = $registration->getCompanions();

        $firstDay = $registration->getStagesRegistrations()->first();
        $lastDay = $registration->getStagesRegistrations()->last();
        if (false === $firstDay || false === $lastDay) {
            return;
        }

        if (false !== $stage = $firstDay->getStage()) {
            $this->stageStart = $stage;
        }
        $this->firstMeal = $firstDay->getFirstMeal();
        if (false !== $stage = $lastDay->getStage()) {
            $this->stageEnd = $stage;
        }
        $this->lastMeal = $lastDay->getLastMeal();
        $this->neededBike = $registration->getNeededBike();
        $this->pricePerDay = $registration->getPricePerDay() / 100;
    }

    /**
     * @return Stage[]
     */
    public function getBookedStages(): array
    {
        $stages = $this->event->getStages()->toArray();

        $startIndex = (int) array_search($this->stageStart, $stages, true);

        return \array_slice(
            $stages,
            $startIndex,
            (int) array_search($this->stageEnd, $stages, true) - $startIndex + 1,
        );
    }

    #[Assert\Callback(groups: ['choose_people'])]
    public function validateNeededBIke(ExecutionContextInterface $context, mixed $payload): void
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
    }
}
