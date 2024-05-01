<?php

declare(strict_types=1);

namespace App\Bridge\Monolog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class EntitiesProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $context = $record->context;
        $extra = $record->extra;

        if (\array_key_exists('user', $context) && $context['user'] instanceof UserInterface) {
            $extra['user'] = $context['user']->getUserIdentifier();
            unset($context['user']);
        }

        return $record->with(context: $context, extra: $extra);
    }
}
