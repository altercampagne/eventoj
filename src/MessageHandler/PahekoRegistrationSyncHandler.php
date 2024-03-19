<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Registration;
use App\Message\PahekoRegistrationSync;
use App\Service\Paheko\UserSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class PahekoRegistrationSyncHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserSynchronizer $userSynchronizer,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(PahekoRegistrationSync $message): void
    {
        if (null === $registration = $this->em->getRepository(Registration::class)->find($message->getRegistrationId())) {
            throw new \LogicException('Given registration does not exists!');
        }

        if (!$registration->isConfirmed()) {
            $this->logger->warning('Cannot sync a non-confirmed registration on Paheko.');

            return;
        }

        $this->userSynchronizer->sync($registration->getUser());
    }
}
