<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RegistrationStatus;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User[]
     */
    public function findAllWithConfirmedReservation(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('u')
            ->from(User::class, 'u')
            ->innerJoin('u.registrations', 'r')
            ->where('r.status = :registration_confirmed')
            ->setParameter('registration_confirmed', RegistrationStatus::CONFIRMED)
        ;

        return $qb->getQuery()->getResult();
    }
}
