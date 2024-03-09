<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity]
#[ORM\Table(name: '`membership`')]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Membership
{
    public const PRICE = 1000;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private readonly UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'memberships')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private readonly ?User $user;

    #[ORM\ManyToOne(targetEntity: Companion::class, inversedBy: 'memberships')]
    #[ORM\JoinColumn(name: 'companion_id', referencedColumnName: 'id', nullable: true)]
    private readonly ?Companion $companion;

    #[ORM\ManyToOne(targetEntity: Payment::class, inversedBy: 'memberships')]
    #[ORM\JoinColumn(name: 'payment_id', referencedColumnName: 'id', nullable: false)]
    private readonly Payment $payment;

    #[ORM\Column(type: Types::INTEGER, options: [
        'comment' => 'The price of this membership',
    ])]
    private readonly int $price;

    #[ORM\Column]
    private readonly \DateTimeImmutable $startAt;

    #[ORM\Column]
    private readonly \DateTimeImmutable $endAt;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt;

    private function __construct(
        Payment $payment,
        ?User $user = null,
        ?Companion $companion = null,
        ?\DateTimeImmutable $startAt = null,
    ) {
        if (null === $user && null === $companion) {
            throw new \LogicException('Membership must be attached to a user or a companion.');
        }
        if (!$payment->isApproved()) {
            throw new \LogicException('Given payment must be approved.');
        }

        if (null === $startAt) {
            $startAt = new \DateTimeImmutable('first day of may');
            if ($startAt > new \DateTimeImmutable()) {
                $startAt = $startAt->modify('-1 year');
            }
        }
        $endAt = $startAt->modify('+1 year -1 day');

        $this->id = new UuidV4();
        $this->payment = $payment;
        $this->user = $user;
        $this->companion = $companion;
        $this->price = self::PRICE;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function createForUser(User $user, Payment $payment, ?\DateTimeImmutable $startAt = null): self
    {
        return new self($payment, user: $user, startAt: $startAt);
    }

    public static function createForCompanion(Companion $companion, Payment $payment, ?\DateTimeImmutable $startAt = null): self
    {
        return new self($payment, companion: $companion, startAt: $startAt);
    }

    public function isValidAt(\DateTimeImmutable $date): bool
    {
        return $this->startAt <= $date && $date <= $this->endAt;
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getCompanion(): ?Companion
    {
        return $this->companion;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function getStartAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }

    public function getEndAt(): \DateTimeImmutable
    {
        return $this->endAt;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
