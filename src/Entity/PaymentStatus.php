<?php

declare(strict_types=1);

namespace App\Entity;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
}
