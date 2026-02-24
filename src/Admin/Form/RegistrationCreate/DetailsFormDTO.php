<?php

declare(strict_types=1);

namespace App\Admin\Form\RegistrationCreate;

use App\Entity\Companion;
use App\Entity\Event;
use App\Entity\Meal;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class DetailsFormDTO
{
    /** @var Collection<int, Companion> */
    public Collection $companions;

    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    public int $bikes = 0;

    #[Assert\NotNull]
    public \DateTimeImmutable $start;

    #[Assert\NotNull]
    public Meal $firstMeal = Meal::LUNCH;

    #[Assert\NotNull]
    #[Assert\GreaterThan(propertyPath: 'start')]
    public \DateTimeImmutable $end;

    #[Assert\NotNull]
    public Meal $lastMeal = Meal::LUNCH;

    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    public int $price;

    public function __construct(
        private readonly Event $event,
        public readonly User $user,
    ) {
    }

    #[Assert\Callback]
    public function validateNeededBike(ExecutionContextInterface $context): void
    {
        if ($this->bikes > \count($this->companions) + 1) {
            $context->buildViolation('Plus d\'un vélo par personne, ça fait beaucoup ! 🤯')
                ->atPath('bikes')
                ->addViolation();
        }
    }

    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        $firstStage = $this->event->getFirstStage();
        $lastStage = $this->event->getLastStage();

        \assert(null !== $firstStage);
        \assert(null !== $lastStage);

        $eventStart = $firstStage->getDate();
        $eventEnd = $lastStage->getDate();

        if ($this->start < $eventStart || $this->start > $eventEnd) {
            $context->buildViolation('La date de début doit être comprise entre le {{ min }} et le {{ max }}.')
                ->atPath('start')
                ->setParameters([
                    '{{ min }}' => $eventStart->format('d/m/Y'),
                    '{{ max }}' => $eventEnd->format('d/m/Y'),
                ])
                ->addViolation();
        }

        if ($this->end < $eventStart || $this->end > $eventEnd) {
            $context->buildViolation('La date de fin doit être comprise entre le {{ min }} et le {{ max }}.')
                ->atPath('end')
                ->setParameters([
                    '{{ min }}' => $eventStart->format('d/m/Y'),
                    '{{ max }}' => $eventEnd->format('d/m/Y'),
                ])
                ->addViolation();
        }
    }
}
