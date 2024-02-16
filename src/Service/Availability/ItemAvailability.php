<?php

declare(strict_types=1);

namespace App\Service\Availability;

use App\Entity\Meal;
use App\Entity\Stage;

final class ItemAvailability
{
    public int $availability;

    public function __construct(
        public readonly Stage $stage,
        public readonly Meal $meal,
        public readonly int $max,
    ) {
        $this->availability = $max;
    }

    public function getPercent(): float
    {
        // This can happen for bikes
        if (0 === $this->max) {
            return 0;
        }

        return (float) ($this->max - $this->availability) * 100 / $this->max;
    }

    public function getBooked(): int
    {
        return $this->max - $this->availability;
    }
}
