<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RegistrationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
#[ORM\Table(name: '`registration`')]
#[ORM\Index(name: 'idx_registration_status', fields: ['status'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Registration
{
    /**
     * This property should be marked as readonly but is not due to a bug in Doctrine.
     *
     * @see https://github.com/doctrine/orm/issues/9863
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'registrations')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private readonly User $user;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'registrations')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: false)]
    private readonly Event $event;

    #[ORM\Column(type: 'string', length: 20, enumType: RegistrationStatus::class, options: [
        'comment' => 'Status of this registration (waiting_payment, confirmed, canceled)',
    ])]
    private RegistrationStatus $status = RegistrationStatus::WAITING_PAYMENT;

    #[ORM\Column(type: Types::INTEGER, options: [
        'comment' => 'The total price choose by the user.',
    ])]
    private int $price = 0;

    #[ORM\Column(type: Types::INTEGER, options: [
        'comment' => 'How many bikes are needed by participants?',
    ])]
    private int $neededBike = 0;

    /**
     * This date is computed at the registration booking date using the age of
     * people at the first stage of this booking (because price can be
     * different when using the date of the booking).
     */
    #[ORM\Column(type: Types::INTEGER, options: [
        'comment' => 'How many children are part of this trip?',
    ])]
    private int $nbChildren = 0;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true, options: [
        'comment' => 'Date on which the reservation was confirmed.',
    ])]
    private ?\DateTimeImmutable $confirmedAt = null;

    #[ORM\Column(nullable: true, options: [
        'comment' => 'Date on which the reservation was canceled.',
    ])]
    private ?\DateTimeImmutable $canceledAt = null;

    /**
     * @var Collection<int, StageRegistration>
     */
    #[ORM\OneToMany(targetEntity: StageRegistration::class, mappedBy: 'registration', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $stagesRegistrations;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'registration')]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $payments;

    /**
     * @var Collection<int, Companion>
     */
    #[ORM\ManyToMany(targetEntity: Companion::class, inversedBy: 'registrations')]
    #[ORM\JoinTable(name: 'registrations_companions')]
    private Collection $companions;

    public function __construct(User $user, Event $event)
    {
        $this->id = Uuid::v7();
        $this->user = $user;
        $this->event = $event;
        $this->createdAt = new \DateTimeImmutable();
        $this->payments = new ArrayCollection();
        $this->companions = new ArrayCollection();

        if ($event->isAT()) {
            $this->stagesRegistrations = new ArrayCollection();
        } else {
            $stagesRegistrations = [];
            $stages = $event->getStages();
            $nbStages = \count($stages);
            for ($i = 0; $i < $nbStages; ++$i) {
                /** @var Stage $stage */
                $stage = $stages[$i];

                $stageRegistration = new StageRegistration($stage, $this);
                if (0 === $i) {
                    $stageRegistration->setPresentForBreakfast($event->firstDayIncludesBreakfast());
                    $stageRegistration->setPresentForLunch($event->firstDayIncludesLunch());
                } elseif ($i === $nbStages - 1) {
                    $stageRegistration->setPresentForLunch($event->lastDayIncludesLunch());
                    $stageRegistration->setPresentForDinner($event->lastDayIncludesDinner());
                }

                $stagesRegistrations[] = $stageRegistration;
            }

            $this->stagesRegistrations = new ArrayCollection($this->orderStagesRegistrations($stagesRegistrations));
        }
    }

    public function confirm(): void
    {
        if (RegistrationStatus::WAITING_PAYMENT !== $this->status) {
            throw new \LogicException("Cannot confirm a registration in {$this->status->value} status.");
        }

        $this->status = RegistrationStatus::CONFIRMED;
        $this->confirmedAt = new \DateTimeImmutable();
    }

    public function isConfirmed(): bool
    {
        return RegistrationStatus::CONFIRMED === $this->status;
    }

    public function isCanceled(): bool
    {
        return RegistrationStatus::CANCELED === $this->status;
    }

    public function canBeCanceled(): bool
    {
        if (RegistrationStatus::CONFIRMED !== $this->status) {
            return false;
        }

        return $this->getCancelationDate() > new \DateTimeImmutable();
    }

    public function getCancelationDate(): \DateTimeImmutable
    {
        if (null === $firstStage = $this->event->getFirstStage()) {
            throw new \RuntimeException('Registration linked to an event without stages!');
        }

        return $firstStage->getDate()->modify('-15 days');
    }

    public function cancel(): void
    {
        if ($this->isCanceled()) {
            throw new \LogicException("Cannot cancel registration {$this->getId()} (already canceled).");
        }

        $this->status = RegistrationStatus::CANCELED;
        $this->canceledAt = new \DateTimeImmutable();
    }

    public function isWaitingPayment(): bool
    {
        return RegistrationStatus::WAITING_PAYMENT === $this->status;
    }

    public function daysOfPresence(): int
    {
        return $this->stagesRegistrations->count();
    }

    public function payingDaysOfPresence(): float
    {
        $payingDaysOfPresence = $this->daysOfPresence() - $this->freeDaysOfPresence();

        if (null === $firstStageRegistration = $this->getStageRegistrationStart()) {
            throw new \LogicException('No stage registration found for given registration');
        }

        if (Meal::BREAKFAST !== $this->getFirstMeal()) {
            $payingDaysOfPresence -= $firstStageRegistration->includesMeal(Meal::LUNCH) ? .2 : .6;
        }

        if (null === $lastStageRegistration = $this->getStageRegistrationEnd()) {
            throw new \LogicException('No stage registration found for given registration');
        }

        if (Meal::DINNER !== $this->getLastMeal()) {
            $payingDaysOfPresence -= $lastStageRegistration->includesMeal(Meal::LUNCH) ? .4 : .8;
        }

        return (float) $payingDaysOfPresence;
    }

    public function freeDaysOfPresence(): int
    {
        return $this->getStagesRegistrations()->filter(static fn (StageRegistration $stageRegistration): bool => $stageRegistration->getStage()->isFree())->count();
    }

    public function getStageRegistrationStart(): ?StageRegistration
    {
        if (false !== $stageRegistration = $this->getStagesRegistrations()->first()) {
            return $stageRegistration;
        }

        return null;
    }

    public function getStageRegistrationEnd(): ?StageRegistration
    {
        if (false !== $stageRegistration = $this->getStagesRegistrations()->last()) {
            return $stageRegistration;
        }

        return null;
    }

    public function countPeople(): int
    {
        return $this->companions->count() + 1;
    }

    public function countChildren(): int
    {
        return $this->nbChildren;
    }

    public function computeChildren(): void
    {
        $this->nbChildren = \count($this->getChildren());
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        if (null === $stageRegistration = $this->getStageRegistrationStart()) {
            return null;
        }

        return $stageRegistration->getStage()->getDate();
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        if (null === $stageRegistration = $this->getStageRegistrationEnd()) {
            return null;
        }

        return $stageRegistration->getStage()->getDate();
    }

    public function isThisYear(): bool
    {
        return $this->getCreatedAt()->format('y') === date('y');
    }

    /**
     * If the first day is a free day, the first meal of the first paying day
     * must be the breakfast.
     */
    public function getFirstMeal(): Meal
    {
        if (null === $firstStageRegistration = $this->getStageRegistrationStart()) {
            throw new \LogicException('No stage registration found for given registration');
        }

        return $firstStageRegistration->getStage()->isFree() ? Meal::BREAKFAST : $firstStageRegistration->getFirstMeal();
    }

    /**
     * If the last day is a free day, the last meal of the last paying day must
     * be the dinner.
     */
    public function getLastMeal(): Meal
    {
        if (null === $lastStageRegistration = $this->getStageRegistrationEnd()) {
            throw new \LogicException('No stage registration found for given registration');
        }

        return $lastStageRegistration->getStage()->isFree() ? Meal::DINNER : $lastStageRegistration->getLastMeal();
    }

    /**
     * @return array<User|Companion>
     */
    public function getPeople(): array
    {
        return array_merge([$this->user], $this->companions->toArray());
    }

    /**
     * @return array<User|Companion>
     */
    public function getNonChildren(): array
    {
        return array_udiff($this->getPeople(), $this->getChildren(), static fn ($person1, $person2): int => $person1 === $person2 ? 0 : -1);
    }

    /**
     * @return array<User|Companion>
     */
    public function getChildren(): array
    {
        return array_filter($this->getPeople(), function (User|Companion $person): bool {
            // If available, we use the date of the first select stage to count
            // children / adults at this date.
            $atDate = new \DateTimeImmutable();
            if (null !== $stageRegistration = $this->getStageRegistrationEnd()) {
                $atDate = $stageRegistration->getStage()->getDate();
            }

            return $person->isChild($atDate);
        });
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getStatus(): RegistrationStatus
    {
        return $this->status;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getNeededBike(): int
    {
        return $this->neededBike;
    }

    public function setNeededBike(int $neededBike): self
    {
        $this->neededBike = $neededBike;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getConfirmedAt(): ?\DateTimeImmutable
    {
        return $this->confirmedAt;
    }

    public function getCanceledAt(): ?\DateTimeImmutable
    {
        return $this->canceledAt;
    }

    /**
     * @return Collection<int, StageRegistration>
     */
    public function getStagesRegistrations(): Collection
    {
        return new ArrayCollection($this->orderStagesRegistrations($this->stagesRegistrations->toArray()));
    }

    /**
     * @param StageRegistration[] $stagesRegistrations
     */
    public function setStagesRegistrations(array $stagesRegistrations): self
    {
        $this->stagesRegistrations = new ArrayCollection($this->orderStagesRegistrations($stagesRegistrations));
        // Number of children at the date of the first stage. That's why it can
        // change when stages change.
        $this->computeChildren();

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        $this->payments->add($payment);

        return $this;
    }

    public function getApprovedPayment(): ?Payment
    {
        return $this->payments->findFirst(static fn (int $key, Payment $payment): bool => $payment->isApproved() || $payment->isRefunded());
    }

    /**
     * @return Collection<int, Companion>
     */
    public function getCompanions(): Collection
    {
        return $this->companions;
    }

    /**
     * @param Collection<int, Companion> $companions
     */
    public function setCompanions(Collection $companions): self
    {
        $this->companions = $companions;
        $this->computeChildren();

        return $this;
    }

    public function addCompanion(Companion $companion): self
    {
        $this->companions->add($companion);

        return $this;
    }

    /**
     * @param StageRegistration[] $stagesRegistrations
     *
     * @return StageRegistration[]
     */
    private function orderStagesRegistrations(array $stagesRegistrations): array
    {
        usort($stagesRegistrations, static function (StageRegistration $sr1, StageRegistration $sr2): int {
            $date1 = $sr1->getStage()->getDate();
            $date2 = $sr2->getStage()->getDate();

            return $date1 == $date2 ? 0 : ($date1 > $date2 ? 1 : -1);
        });

        return $stagesRegistrations;
    }
}
