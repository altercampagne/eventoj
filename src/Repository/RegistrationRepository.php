<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Registration;
use App\Entity\RegistrationStatus;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Registration>
 */
class RegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Registration::class);
    }

    public function findOngoingRegistrationForEventAndUser(Event $event, User $user): ?Registration
    {
        return $this->findRegistrationForEventAndUser($event, $user, RegistrationStatus::WAITING_PAYMENT);
    }

    public function findConfirmedRegistrationForEventAndUser(Event $event, User $user): ?Registration
    {
        return $this->findRegistrationForEventAndUser($event, $user, RegistrationStatus::CONFIRMED);
    }

    private function findRegistrationForEventAndUser(Event $event, User $user, RegistrationStatus $status): ?Registration
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('r')
            ->from(Registration::class, 'r')
            ->andWhere('r.event = :event')
            ->andWhere('r.user = :user')
            ->andWhere('r.status = :status')
            ->setParameter('event', $event)
            ->setParameter('user', $user)
            ->setParameter('status', $status)
        ;

        /* @phpstan-ignore-next-line */
        return $qb->getQuery()->getOneOrNullResult();
    }
}
