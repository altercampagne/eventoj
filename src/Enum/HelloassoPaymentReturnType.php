<?php

declare(strict_types=1);

namespace App\Enum;

enum HelloassoPaymentReturnType: string
{
    case Back = 'back';
    case Error = 'error';
    case Return = 'return';
}
