<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;

trait DatabaseUtilTrait
{
    protected function getRandomUser(): User
    {
        /** @var UserRepository $repository */
        $repository = static::getContainer()->get(UserRepository::class);

        $users = $repository->findAll();

        return $users[array_rand($users)];
    }

    protected function getRandomAdminUser(): User
    {
        /** @var UserRepository $repository */
        $repository = static::getContainer()->get(UserRepository::class);

        /** @var ?User $user */
        $user = $repository->createQueryBuilder('u')
            ->andWhere(':role_admin = ANY_OF(u.roles)')
            ->setMaxResults(1)
            ->setParameter('role_admin', 'ROLE_ADMIN')
            ->getQuery()
            ->getSingleResult()
        ;

        if (null === $user) {
            throw new \RuntimeException('No admin user found!');
        }

        return $user;
    }

    protected function getBookableEvent(): Event
    {
        /** @var EventRepository $repository */
        $repository = static::getContainer()->get(EventRepository::class);

        /** @var ?Event $event */
        $event = $repository->createQueryBuilder('e')
            ->innerJoin('e.stages', 's')
            ->andWhere('e.openingDateForBookings < :now')
            ->andWhere('s.date > :now')
            ->setMaxResults(1)
            ->setParameter('now', new \DateTimeImmutable(), Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getSingleResult()
        ;

        if (null === $event) {
            throw new \RuntimeException('No bookable event found!');
        }

        return $event;
    }
}
