<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity]
#[ORM\Table(name: '`registration`')]
class Registration
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private readonly UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'registrations')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private readonly User $user;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'registrations')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id')]
    private readonly Event $event;

    #[ORM\Column(type: 'string', length: 20, enumType: RegistrationStatus::class, options: [
        'comment' => 'Status of this registration (waiting_payment, confirmed, canceled)',
    ])]
    private RegistrationStatus $status;

    #[ORM\Column(type: 'string', length: 10, enumType: Meal::class, options: [
        'comment' => 'First meal participant will share with us (breakfast, lunch, dinner)',
    ])]
    private readonly Meal $firstMeal;

    #[ORM\Column(type: 'string', length: 10, enumType: Meal::class, options: [
        'comment' => 'Last meal participant will share with us (breakfast, lunch, dinner)',
    ])]
    private readonly Meal $lastMeal;

    #[ORM\Column(type: Types::INTEGER, options: [
        'comment' => 'The price per day choose by the user.',
    ])]
    private readonly int $pricePerDay;

    #[ORM\Column(type: Types::BOOLEAN, options: [
        'comment' => 'Does the participant need a loan bike?',
    ])]
    private readonly bool $needBike;

    #[ORM\Column(type: Types::STRING, nullable: true, options: [
        'comment' => 'The checkout intent ID provided by Helloasso',
    ])]
    private ?string $helloassoCheckoutIntentId = null;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true, options: [
        'comment' => 'Date on which the reservation was confirmed.',
    ])]
    private \DateTimeImmutable $confirmedAt;

    #[ORM\Column(nullable: true, options: [
        'comment' => 'Date on which the reservation was cancelled.',
    ])]
    private \DateTimeImmutable $canceledAt;

    /**
     * @var Collection<int, Stage>
     */
    #[ORM\ManyToMany(targetEntity: Stage::class, inversedBy: 'registrations')]
    #[ORM\JoinTable(name: 'stages_registrations')]
    #[ORM\OrderBy(['date' => 'ASC'])]
    private Collection $stages;

    /**
     * @param Stage[] $stages
     */
    public function __construct(User $user, Event $event, array $stages, Meal $firstMeal, Meal $lastMeal, int $pricePerDay, bool $needBike)
    {
        $this->id = new UuidV4();
        $this->user = $user;
        $this->event = $event;
        $this->stages = new ArrayCollection($stages);
        $this->firstMeal = $firstMeal;
        $this->lastMeal = $lastMeal;
        $this->pricePerDay = $pricePerDay;
        $this->needBike = $needBike;
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

    public function isCanceled(): bool
    {
        return RegistrationStatus::CANCELED === $this->status;
    }

    public function canBeCanceled(): bool
    {
        if (RegistrationStatus::CANCELED === $this->status) {
            return false;
        }

        return $this->event->isBookable();
    }

    public function cancel(): void
    {
        if (!$this->canBeCanceled()) {
            throw new \LogicException('Cannot cancel this registration.');
        }

        $this->status = RegistrationStatus::CANCELED;
        $this->canceledAt = new \DateTimeImmutable();
    }

    public function countDaysOfPresence(): int
    {
        return $this->stages->count() - 1;
    }

    public function getTotalPrice(): int
    {
        return $this->countDaysOfPresence() * $this->pricePerDay;
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

    public function getFirstMeal(): Meal
    {
        return $this->firstMeal;
    }

    public function getLastMeal(): Meal
    {
        return $this->lastMeal;
    }

    public function getPricePerDay(): int
    {
        return $this->pricePerDay;
    }

    public function needBike(): bool
    {
        return $this->needBike;
    }

    public function getHelloassoCheckoutIntentId(): ?string
    {
        return $this->helloassoCheckoutIntentId;
    }

    public function setHelloassoCheckoutIntentId(string $helloassoCheckoutIntentId): self
    {
        $this->helloassoCheckoutIntentId = $helloassoCheckoutIntentId;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getConfirmedAt(): \DateTimeImmutable
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(\DateTimeImmutable $confirmedAt): self
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    public function getCanceledAt(): \DateTimeImmutable
    {
        return $this->canceledAt;
    }

    public function setCanceledAt(\DateTimeImmutable $canceledAt): self
    {
        $this->canceledAt = $canceledAt;

        return $this;
    }

    /**
     * @return Collection<int, Stage>
     */
    public function getStages(): Collection
    {
        return $this->stages;
    }
}
