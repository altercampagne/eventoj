<?php

declare(strict_types=1);

namespace App\Entity;

enum UploadedFileType: string
{
    case EVENT = 'event';
    case ALTERNATIVE = 'alternative';
}
