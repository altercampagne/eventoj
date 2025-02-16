<?php

declare(strict_types=1);

namespace App\Entity\Document;

enum UploadedImageType: string
{
    case EVENT = 'event';
    case ALTERNATIVE = 'alternative';
}
