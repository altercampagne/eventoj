<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RegistrationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Uid\UuidV4;

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
    private UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'registrations')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private readonly User $user;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'registrations')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: false)]
    private readonly Event $event;

    #[ORM\Column(type: 'string', length: 20, enumType: RegistrationStatus::class, options: [
        'comment' => 'Status of this registration (waiting_payment, confirmed, canceled)',
    ])]
    private RegistrationStatus $status;

    #[ORM\Column(type: Types::INTEGER, options: [
        'comment' => 'The total price choose by the user.',
    ])]
    private int $price = 0;

    #[ORM\Column(type: Types::INTEGER, options: [
        'comment' => 'How many bikes are needed by participants?',
    ])]
    private int $neededBike = 0;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt;

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
        $this->id = new UuidV4();
        $this->user = $user;
        $this->event = $event;
        $this->status = RegistrationStatus::WAITING_PAYMENT;
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
            $this->stagesRegistrations = new ArrayCollection($stagesRegistrations);
        }
    }

    public function canBeConfirmed(): bool
    {
        if (RegistrationStatus::WAITING_PAYMENT !== $this->status) {
            return false;
        }

        return $this->event->isBookable();
    }

    public function confirm(): void
    {
        if (!$this->canBeConfirmed()) {
            throw new \LogicException('Cannot confirm this registration.');
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
        if (!$this->canBeCanceled()) {
            throw new \LogicException('Cannot cancel this registration.');
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
        $payingDaysOfPresence = $this->stagesRegistrations->count() - $this->freeDaysOfPresence();

        if (false === $firstStageRegistration = $this->stagesRegistrations->first()) {
            throw new \LogicException('No stage registration found for given registration');
        }
        // If the first day is a free day, the first meal of the first paying day must be the breakfast
        $firstMeal = $firstStageRegistration->getStage()->isFree() ? Meal::BREAKFAST : $firstStageRegistration->getFirstMeal();
        if (Meal::BREAKFAST !== $firstMeal) {
            $payingDaysOfPresence -= $firstStageRegistration->includesMeal(Meal::LUNCH) ? .2 : .6;
        }

        if (false === $lastStageRegistration = $this->stagesRegistrations->last()) {
            throw new \LogicException('No stage registration found for given registration');
        }
        // If the last day is a free day, the last meal of the last paying day must be the dinner
        $lastMeal = $lastStageRegistration->getStage()->isFree() ? Meal::DINNER : $lastStageRegistration->getLastMeal();
        if (Meal::DINNER !== $lastMeal) {
            $payingDaysOfPresence -= $lastStageRegistration->includesMeal(Meal::LUNCH) ? .4 : .8;
        }

        return (float) $payingDaysOfPresence;
    }

    public function freeDaysOfPresence(): int
    {
        return $this->stagesRegistrations->filter(static function (StageRegistration $stageRegistration): bool {
            return $stageRegistration->getStage()->isFree();
        })->count();
    }

    public function getStageRegistrationStart(): ?StageRegistration
    {
        if (false !== $stageRegistration = $this->stagesRegistrations->first()) {
            return $stageRegistration;
        }

        return null;
    }

    public function getStageRegistrationEnd(): ?StageRegistration
    {
        if (false !== $stageRegistration = $this->stagesRegistrations->last()) {
            return $stageRegistration;
        }

        return null;
    }

    public function countPeople(): int
    {
        return \count($this->getPeople());
    }

    public function countChildren(): int
    {
        return \count($this->getChildren());
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        if (null === $stageRegistration = $this->getStageRegistrationEnd()) {
            return null;
        }

        return $stageRegistration->getStage()->getDate();
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

    public function getId(): UuidV4
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
        return $this->stagesRegistrations;
    }

    /**
     * @param StageRegistration[] $stagesRegistrations
     */
    public function setStagesRegistrations(array $stagesRegistrations): self
    {
        $this->stagesRegistrations = new ArrayCollection($stagesRegistrations);

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
        return $this->payments->findFirst(static function (int $key, Payment $payment): bool {
            return $payment->isApproved() || $payment->isRefunded();
        });
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

        return $this;
    }

    public function addCompanion(Companion $companion): self
    {
        $this->companions->add($companion);

        return $this;
    }
}
