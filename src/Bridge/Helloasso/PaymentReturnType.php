<?php

declare(strict_types=1);

namespace App\Bridge\Helloasso;

enum PaymentReturnType: string
{
    case Back = 'back';
    case Error = 'error';
    case Return = 'return';
}
