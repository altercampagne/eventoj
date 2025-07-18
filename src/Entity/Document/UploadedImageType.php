<?php

declare(strict_types=1);

namespace App\Entity\Document;

enum UploadedImageType: string
{
    // This correspond to the image uploaded from admin to illustrate events.
    // Totally different from event pictures uploaded by members.
    case EVENT = 'event';

    case ALTERNATIVE = 'alternative';
}
