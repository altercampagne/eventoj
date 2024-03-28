<?php

declare(strict_types=1);

namespace App\Bridge\Monolog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class EventojContextProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        if (null !== $user = $this->security->getUser()) {
            $record->extra['user'] = $user->getUserIdentifier();
        }

        return $record;
    }
}
