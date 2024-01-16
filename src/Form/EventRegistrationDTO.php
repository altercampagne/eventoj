<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Event;
use App\Entity\Meal;
use App\Entity\Registration;
use App\Entity\Stage;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EventRegistrationDTO
{
    public Event $event;

    #[Assert\NotBlank]
    public ?Stage $stageStart = null;
    #[Assert\NotBlank]
    public Meal $firstMeal = MEAL::DINNER;
    #[Assert\NotBlank]
    public ?Stage $stageEnd = null;
    #[Assert\NotBlank]
    public Meal $lastMeal = Meal::BREAKFAST;
    #[Assert\NotNull]
    public bool $needBike = false;
    #[Assert\NotBlank]
    #[Assert\Range(min: 10, minMessage: 'Le prix minimum par jour est de {{ limit }} €.')]
    public int $pricePerDay = 33;

    public function __construct(Event $event, Registration $registration = null)
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

        if (null !== $registration) {
            if (false !== $stage = $registration->getStages()->first()) {
                $this->stageStart = $stage;
            }
            $this->firstMeal = $registration->getFirstMeal();
            if (false !== $stage = $registration->getStages()->last()) {
                $this->stageEnd = $stage;
            }
            $this->lastMeal = $registration->getLastMeal();
            $this->needBike = $registration->needBike();
            $this->pricePerDay = $registration->getPricePerDay() / 100;
        }
    }

    #[Assert\Callback]
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
        if ($numberOfDays < 4) {
            $context->buildViolation('Tu dois rester au minimum 4 jours sur l\'évènement.')
                ->addViolation();
        }
    }
}
