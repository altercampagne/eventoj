<?php

declare(strict_types=1);

namespace App\Bridge\Monolog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

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
        unset($context['exception']);

        return $record->with(context: $context, extra: $extra);
    }
}
