<?php

declare(strict_types=1);

namespace App\Admin\Security;

enum Permission: string
{
    case ADMIN_ACCESS = 'admin_access';
    case DEBUG = 'debug';
    case SHOW_PAHEKO_DETAILS = 'show_paheko_details';
    case IMAGE_DOWNLOAD_ORIGINAL = 'image_download_original';

    case ALTERNATIVE_LIST = 'alternative.list';
    case ALTERNATIVE_VIEW = 'alternative.view';
    case ALTERNATIVE_CREATE = 'alternative.create';
    case ALTERNATIVE_UPDATE = 'alternative.update';
    case ALTERNATIVE_DELETE = 'alternative.delete';

    case EVENT_LIST = 'event.list';
    case EVENT_VIEW_REGISTRATIONS = 'event.view_registrations';
    case EVENT_VIEW_STAGES = 'event.view_stages';
    case EVENT_VIEW_FILLING = 'event.view_filling';
    case EVENT_VIEW_MEALS = 'event.view_meals';
    case EVENT_VIEW_ARRIVALS = 'event.view_arrivals';
    case EVENT_CREATE = 'event.create';
    case EVENT_UPDATE = 'event.update';
    case EVENT_PUBLISH = 'event.publish';
    case EVENT_UPDATE_AVAILABILITY = 'event.event_update_availability';
    case EVENT_EXPORT_EMAILS = 'event.export_emails';
    case EVENT_FOOD_CALCULATOR = 'event.food_calculator';

    case MEMBERSHIP_LIST = 'membership.list';

    case PAYMENT_LIST = 'payment.list';
    case PAYMENT_VIEW = 'payment.view';
    case PAYMENT_SYNC_WITH_HELLOASSO = 'payment.sync_with_helloasso';
    case PAYMENT_SYNC_WITH_PAHEKO = 'payment.sync_with_paheko';
    case PAYMENT_REFUND = 'payment.refund';

    case QUESTION_LIST = 'question.list';
    case QUESTION_VIEW = 'question.view';
    case QUESTION_CREATE = 'question.create';
    case QUESTION_UPDATE = 'question.update';
    case QUESTION_DELETE = 'question.delete';

    case REGISTRATION_LIST = 'registration.list';
    case REGISTRATION_VIEW = 'registration.view';
    case REGISTRATION_BIKE_ADD = 'registration.bike_add';
    case REGISTRATION_BIKE_DELETE = 'registration.bike_delete';
    case REGISTRATION_CONFIRMATION_EMAIL_SEND = 'registration.confirmation_email_send';

    case STAGE_CREATE = 'stage.create';
    case STAGE_UPDATE = 'stage.update';
    case STAGE_VIEW = 'stage.view';

    case USER_LIST = 'user.list';
    case USER_VIEW = 'user.view';
    case USER_MANAGEMENT = 'user.management';
    case USER_VERIFY_EMAIL = 'user.verify_email';
    case USER_MEMBERSHIP_CREATE = 'user.membership_create'; // Used to offer a membership to a user (free or linked to a cash payment)
    case USER_SYNC_WITH_PAHEKO = 'user.sync_with_paheko';
    case USER_REMOVE_DIET_DETAILS = 'user.remove_diet_details';
}
