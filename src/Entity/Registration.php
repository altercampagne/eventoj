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
        'comment' => 'Status of this registration (waiting_payment, confirmed)',
    ])]
    private RegistrationStatus $status;

    #[ORM\Column(type: Types::INTEGER, options: [
        'comment' => 'The total price choose by the user.',
    ])]
    private int $price;

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
            foreach ($event->getStages() as $stage) {
                $stagesRegistrations[] = new StageRegistration($stage, $this);
            }
            $this->stagesRegistrations = new ArrayCollection($stagesRegistrations);
        }

        $this->price = $event->getBreakEvenPricePerDay() * $this->payingDaysOfPresence();
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

    public function isWaitingPayment(): bool
    {
        return RegistrationStatus::WAITING_PAYMENT === $this->status;
    }

    public function daysOfPresence(): int
    {
        return $this->stagesRegistrations->count();
    }

    public function payingDaysOfPresence(): int
    {
        return $this->stagesRegistrations->count() - $this->freeDaysOfPresence();
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
        return $this->companions->count() + 1;
    }

    public function countChildren(): int
    {
        $count = 0;
        foreach ($this->companions as $companion) {
            if ($companion->isChild()) {
                ++$count;
            }
        }

        return $count;
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

    public function setConfirmedAt(\DateTimeImmutable $confirmedAt): self
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
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
            return $payment->isApproved();
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
