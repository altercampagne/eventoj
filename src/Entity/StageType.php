<?php

declare(strict_types=1);

namespace App\Entity;

enum StageType: string
{
    case BEFORE = 'before';
    case AFTER = 'after';
    case CLASSIC = 'classic';
}
