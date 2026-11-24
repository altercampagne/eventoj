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
        if (($user = $this->security->getUser()) instanceof \Symfony\Component\Security\Core\User\UserInterface) {
            $record->extra['current_user_identifier'] = $user->getUserIdentifier();
        }

        return $record;
    }
}
