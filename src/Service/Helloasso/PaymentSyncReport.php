<?php

declare(strict_types=1);

namespace App\Service\Helloasso;

final readonly class PaymentSyncReport
{
    private const string STATUS_WARNING = 'warning';

    private const string STATUS_NOTHING_DONE = 'nothing_done';

    private const string STATUS_UPDATED = 'updated';

    private const string STATUS_EXPIRED = 'expired';

    private function __construct(
        private readonly string $status,
        public readonly string $message,
    ) {
    }

    public static function warning(string $message): self
    {
        return new self(self::STATUS_WARNING, $message);
    }

    public static function nothingDone(string $message): self
    {
        return new self(self::STATUS_NOTHING_DONE, $message);
    }

    public static function updated(string $message): self
    {
        return new self(self::STATUS_UPDATED, $message);
    }

    public static function expired(string $message): self
    {
        return new self(self::STATUS_EXPIRED, $message);
    }

    public function hasBeenUpdated(): bool
    {
        return self::STATUS_UPDATED === $this->status;
    }

    public function hasBeenExpired(): bool
    {
        return self::STATUS_EXPIRED === $this->status;
    }

    public function isWarning(): bool
    {
        return self::STATUS_WARNING === $this->status;
    }

    public function nothingHasBeenDone(): bool
    {
        return self::STATUS_NOTHING_DONE === $this->status;
    }
}
