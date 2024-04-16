<?php

declare(strict_types=1);

namespace App\Entity;

enum RegistrationStatus: string
{
    case WAITING_PAYMENT = 'waiting_payment';
    case CONFIRMED = 'confirmed';
    case CANCELED = 'canceled';
}
