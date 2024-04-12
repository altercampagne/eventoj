<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Registration;
use App\Entity\RegistrationStatus;
use App\Entity\Stage;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Registration>
 *
 * @method Registration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Registration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Registration[]    findAll()
 * @method Registration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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

    /**
     * @return Stage[]
     */
    public function findConfirmedStagesForEventAndUser(Event $event, User $user): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('s')
            ->from(Stage::class, 's')
            ->innerJoin('s.stagesRegistrations', 'sr')
            ->innerJoin('sr.registration', 'r')
            ->andWhere('s.event = :event')
            ->andWhere('r.user = :user')
            ->andWhere('r.status = :status')
            ->setParameter('event', $event)
            ->setParameter('user', $user)
            ->setParameter('status', RegistrationStatus::CONFIRMED)
        ;

        return $qb->getQuery()->getResult();
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
