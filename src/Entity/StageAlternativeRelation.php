<?php

declare(strict_types=1);

namespace App\Entity;

enum StageAlternativeRelation: string
{
    /**
     * This alternative is the departure point of the stage.
     */
    case DEPARTURE = 'departure';

    /**
     * This alternative is the arrival point of the stage.
     */
    case ARRIVAL = 'arrival';

    /**
     * This alternative will be visited during the stage.
     */
    case VISIT = 'visit';

    /**
     * We'll stay on the given alternative during the whole day.
     */
    case FULL_DAY = 'full_day';
}
