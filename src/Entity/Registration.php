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

    #[ORM\Column(type: Types::INTEGER, options: [
        'comment' => 'The price per day choose by the user.',
    ])]
    private readonly int $pricePerDay;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true, options: [
        'comment' => 'Date on which the reservation was confirmed.',
    ])]
    private \DateTimeImmutable $confirmedAt;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true, options: [
        'comment' => 'Date on which the reservation was cancelled.',
    ])]
    private \DateTimeImmutable $canceledAt;

    /**
     * @var Collection<int, Stage>
     */
    #[ORM\ManyToMany(targetEntity: Stage::class, inversedBy: 'registrations')]
    #[ORM\JoinTable(name: 'stages_registrations')]
    private Collection $stages;

    /**
     * @param Stage[] $stages
     */
    public function __construct(User $user, Event $event, array $stages, int $pricePerDay)
    {
        $this->id = new UuidV4();
        $this->user = $user;
        $this->event = $event;
        $this->stages = new ArrayCollection($stages);
        $this->pricePerDay = $pricePerDay;
        $this->status = RegistrationStatus::WAITING_PAYMENT;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function confirm(): void
    {
        if (RegistrationStatus::WAITING_PAYMENT !== $this->status) {
            throw new \LogicException('Cannot confirm a registration which is not in "WAITING_PAYMENT" status.');
        }

        $this->status = RegistrationStatus::CONFIRMED;
        $this->confirmedAt = new \DateTimeImmutable();
    }

    public function cancel(): void
    {
        $this->status = RegistrationStatus::CANCELED;
        $this->canceledAt = new \DateTimeImmutable();
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
}
