<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RegistrationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
#[ORM\Table(name: '`registration`')]
#[ORM\Index(name: 'idx_registration_status', fields: ['status'])]
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
        'comment' => 'The price per day choose by the user.',
    ])]
    private int $pricePerDay = 33;

    #[ORM\Column(type: Types::BOOLEAN, options: [
        'comment' => 'Does the participant need a loan bike?',
    ])]
    private bool $needBike = false;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true, options: [
        'comment' => 'Date on which the reservation was confirmed.',
    ])]
    private ?\DateTimeImmutable $confirmedAt = null;

    /**
     * @var Collection<int, StageRegistration>
     */
    #[ORM\OneToMany(targetEntity: StageRegistration::class, mappedBy: 'registration', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $stagesRegistrations;

    public function __construct(User $user, Event $event)
    {
        $this->id = new UuidV4();
        $this->user = $user;
        $this->event = $event;
        $this->stagesRegistrations = new ArrayCollection();
        $this->status = RegistrationStatus::WAITING_PAYMENT;
        $this->createdAt = new \DateTimeImmutable();
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

    public function countDaysOfPresence(): int
    {
        return $this->stagesRegistrations->count() - 1;
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

    public function getPricePerDay(): int
    {
        return $this->pricePerDay;
    }

    public function setPricePerDay(int $pricePerDay): self
    {
        $this->pricePerDay = $pricePerDay;

        return $this;
    }

    public function needBike(): bool
    {
        return $this->needBike;
    }

    public function setNeedBike(bool $needBike): self
    {
        $this->needBike = $needBike;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
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

    public function getFirstStageRegistration(): ?StageRegistration
    {
        if (false === $stagesRegistration = $this->stagesRegistrations->first()) {
            return null;
        }

        return $stagesRegistration;
    }

    public function getLastStageRegistration(): ?StageRegistration
    {
        if (false === $stagesRegistration = $this->stagesRegistrations->last()) {
            return null;
        }

        return $stagesRegistration;
    }
}
