<?php

declare(strict_types=1);

namespace App\Entity;

enum EventType: string
{
    case AT = 'AT';
    case ADT = 'ADT';
    case BT = 'BT';
    case EB = 'EB';
}
