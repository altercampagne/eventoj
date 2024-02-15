<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity]
#[ORM\Table(name: '`payment`')]
#[ORM\Index(name: 'idx_payment_status', fields: ['status'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Payment
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private readonly UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(name: 'payer_id', referencedColumnName: 'id', nullable: false)]
    private readonly User $payer;

    #[ORM\ManyToOne(targetEntity: Registration::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(name: 'registration_id', referencedColumnName: 'id', nullable: false)]
    private readonly Registration $registration;

    #[ORM\Column(type: 'string', length: 20, enumType: PaymentStatus::class, options: [
        'comment' => 'Status of this payment (pending, approved, failed)',
    ])]
    private PaymentStatus $status;

    #[ORM\Column(type: Types::INTEGER, options: [
        'comment' => 'The amount of thie payment',
    ])]
    private readonly int $amount;

    #[ORM\Column(type: Types::STRING, nullable: true, options: [
        'comment' => 'The checkout intent ID provided by Helloasso',
    ])]
    private ?string $helloassoCheckoutIntentId = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $approvedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $failedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $refundedAt = null;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt;

    public function __construct(User $payer, int $amount, Registration $registration)
    {
        $this->id = new UuidV4();
        $this->payer = $payer;
        $this->amount = $amount;
        $this->registration = $registration;
        $this->status = PaymentStatus::PENDING;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function approve(): void
    {
        $this->status = PaymentStatus::APPPROVED;
        $this->approvedAt = new \DateTimeImmutable();
    }

    public function fail(): void
    {
        $this->status = PaymentStatus::FAILED;
        $this->approvedAt = new \DateTimeImmutable();
    }

    public function isPending(): bool
    {
        return PaymentStatus::PENDING === $this->status;
    }

    public function isApproved(): bool
    {
        return PaymentStatus::APPPROVED === $this->status;
    }

    public function isFailed(): bool
    {
        return PaymentStatus::FAILED === $this->status;
    }

    public function isRefunded(): bool
    {
        return PaymentStatus::REFUNDED === $this->status;
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getPayer(): User
    {
        return $this->payer;
    }

    public function getRegistration(): Registration
    {
        return $this->registration;
    }

    public function getStatus(): PaymentStatus
    {
        return $this->status;
    }

    public function getAmount(): int
    {
        return $this->amount;
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

    public function getApprovedAt(): ?\DateTimeImmutable
    {
        return $this->approvedAt;
    }

    public function getFailedAt(): ?\DateTimeImmutable
    {
        return $this->failedAt;
    }

    public function getRefundedAt(): ?\DateTimeImmutable
    {
        return $this->refundedAt;
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
