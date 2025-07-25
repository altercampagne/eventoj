<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    /**
     * This property should be marked as readonly but is not due to a bug in Doctrine.
     *
     * @see https://github.com/doctrine/orm/issues/9863
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(name: 'payer_id', referencedColumnName: 'id', nullable: false)]
    private readonly User $payer;

    #[ORM\ManyToOne(targetEntity: Registration::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(name: 'registration_id', referencedColumnName: 'id', nullable: true)]
    private readonly ?Registration $registration;

    #[ORM\Column(type: 'string', length: 20, enumType: PaymentStatus::class, options: [
        'comment' => 'Status of this payment (pending, approved, failed, refunded)',
    ])]
    private PaymentStatus $status = PaymentStatus::PENDING;

    #[ORM\Column(type: Types::INTEGER, options: [
        'comment' => 'The amount of this payment',
    ])]
    private readonly int $amount;

    #[ORM\Column(type: Types::STRING, nullable: true, options: [
        'comment' => 'The checkout intent ID provided by Helloasso',
    ])]
    private ?string $helloassoCheckoutIntentId = null;

    #[ORM\Column(type: Types::STRING, nullable: true, options: [
        'comment' => 'The order ID provided by Helloasso when a checkout is successful',
    ])]
    private ?string $helloassoOrderId = null;

    #[ORM\Column(unique: true, nullable: true)]
    private ?string $pahekoPaymentId = null;

    #[ORM\Column(unique: true, nullable: true)]
    private ?string $pahekoRefundId = null;

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
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expiredAt = null;

    /**
     * @var Collection<int, Membership>
     */
    #[ORM\OneToMany(targetEntity: Membership::class, mappedBy: 'payment', cascade: ['persist'])]
    private Collection $memberships;

    #[ORM\Column]
    private readonly int $instalments;

    public function __construct(User $payer, int $amount, ?Registration $registration = null, int $instalments = 1)
    {
        $this->id = new UuidV4();
        $this->payer = $payer;
        $this->amount = $amount;
        $this->registration = $registration;
        $this->instalments = $instalments;
        $this->createdAt = new \DateTimeImmutable();
        $this->memberships = new ArrayCollection();

        if (null !== $registration) {
            $registration->addPayment($this);
        }
    }

    public function isThisYear(): bool
    {
        return $this->getCreatedAt()->format('y') === date('y');
    }

    /**
     * The given approval date must be the date of the order in helloasso.
     */
    public function approve(string $helloassoOrderId, \DateTimeImmutable $approvedAt): void
    {
        $this->helloassoOrderId = $helloassoOrderId;
        $this->status = PaymentStatus::APPROVED;
        $this->approvedAt = $approvedAt;
    }

    /**
     * This method have been added to correct wrong dates in DB when syncyng a
     * payment with helloasso.
     */
    public function setApprovedAt(\DateTimeImmutable $approvedAt): void
    {
        $this->approvedAt = $approvedAt;
    }

    public function fail(): void
    {
        $this->status = PaymentStatus::FAILED;
        $this->failedAt = new \DateTimeImmutable();
    }

    public function isPending(): bool
    {
        return PaymentStatus::PENDING === $this->status;
    }

    public function isApproved(): bool
    {
        return PaymentStatus::APPROVED === $this->status;
    }

    public function isFailed(): bool
    {
        return PaymentStatus::FAILED === $this->status;
    }

    public function isRefunded(): bool
    {
        return PaymentStatus::REFUNDED === $this->status;
    }

    public function isExpired(): bool
    {
        return PaymentStatus::EXPIRED === $this->status;
    }

    public function refund(): void
    {
        $this->status = PaymentStatus::REFUNDED;
        $this->refundedAt = new \DateTimeImmutable();
    }

    public function expire(): void
    {
        $this->status = PaymentStatus::EXPIRED;
        $this->expiredAt = new \DateTimeImmutable();
    }

    public function getMembershipsAmount(): int
    {
        $amount = 0;

        foreach ($this->memberships as $membership) {
            $amount += $membership->getPrice();
        }

        return $amount;
    }

    /**
     * Return the price paid for the registration only (does not include the
     * price paid for memberships if any).
     */
    public function getRegistrationOnlyAmount(): int
    {
        if (null === $this->registration) {
            return 0;
        }

        return $this->amount - $this->getMembershipsAmount();
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getPayer(): User
    {
        return $this->payer;
    }

    public function getRegistration(): ?Registration
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

    public function getAmountWithoutMemberships(): int
    {
        $amount = $this->amount;
        foreach ($this->memberships as $membership) {
            $amount -= $membership->getPrice();
        }

        return $amount;
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

    public function getHelloassoOrderId(): ?string
    {
        return $this->helloassoOrderId;
    }

    public function setHelloassoOrderId(string $helloassoOrderId): self
    {
        $this->helloassoOrderId = $helloassoOrderId;

        return $this;
    }

    public function getPahekoPaymentId(): ?string
    {
        return $this->pahekoPaymentId;
    }

    public function setPahekoPaymentId(string $pahekoPaymentId): self
    {
        $this->pahekoPaymentId = $pahekoPaymentId;

        return $this;
    }

    public function getPahekoRefundId(): ?string
    {
        return $this->pahekoRefundId;
    }

    public function setPahekoRefundId(?string $pahekoRefundId): self
    {
        $this->pahekoRefundId = $pahekoRefundId;

        return $this;
    }

    public function withInstalments(): bool
    {
        return $this->instalments > 1;
    }

    public function getInstalments(): int
    {
        return $this->instalments;
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

    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expiredAt;
    }

    /**
     * @return Collection<int, Membership>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }
}
