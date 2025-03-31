<?php

declare(strict_types=1);

namespace App\Bridge\Monolog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * This processor expand the exception found in context.exception to display
 * details into extra.exception.
 *
 * The exception MUST not be removed from the context, otherwise it breaks the
 * behavior of the HttpCodeActivationStrategy class (when using monolog
 * fingers_crossed handler & excluded_http_codes option)
 */
final readonly class ExceptionProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $context = $record->context;
        $extra = $record->extra;

        $e = $context['exception'] ?? null;
        if (!$e instanceof \Exception) {
            return $record;
        }

        $extra['exception'] = [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ];

        return $record->with(context: $context, extra: $extra);
    }
}
