<?php

declare(strict_types=1);

namespace App\Admin\Security;

enum Permission: string
{
    case ALTERNATIVE_LIST = 'alternative.list';
    case ALTERNATIVE_VIEW = 'alternative.view';
    case ALTERNATIVE_CREATE = 'alternative.create';
    case ALTERNATIVE_UPDATE = 'alternative.update';

    case EVENT_LIST = 'event.list';
    case EVENT_VIEW_REGISTRATIONS = 'event.view_registrations';
    case EVENT_VIEW_STAGES = 'event.view_stages';
    case EVENT_VIEW_FILLING = 'event.view_filling';
    case EVENT_VIEW_MEALS = 'event.view_meals';
    case EVENT_VIEW_ARRIVALS = 'event.view_arrivals';
    case EVENT_CREATE = 'event.create';
    case EVENT_UPDATE = 'event.update';
    case EVENT_PUBLISH = 'event.publish';

    case PAYMENT_LIST = 'payment.list';
    case PAYMENT_VIEW = 'payment.view';

    case REGISTRATION_LIST = 'registration.list';
    case REGISTRATION_VIEW = 'registration.view';

    case STAGE_LIST = 'stage.list';
    case STAGE_CREATE = 'stage.create';
    case STAGE_UPDATE = 'stage.update';

    case USER_LIST = 'user.list';
    case USER_VIEW = 'user.view';
    case USER_PROMOTE = 'user.promote';
    case USER_UNPROMOTE = 'user.unpromote';
}
