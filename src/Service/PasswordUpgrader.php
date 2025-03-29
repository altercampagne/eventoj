<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

final readonly class PasswordUpgrader implements PasswordUpgraderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            $this->logger->warning('Received an unknonw user, unable to upgrade password.', [
                'user_instanceof' => $user::class,
            ]);

            return;
        }

        $user->setPassword($newHashedPassword);

        $this->em->persist($user);
        $this->em->flush();
    }
}
