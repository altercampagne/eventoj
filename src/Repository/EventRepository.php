<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findOneBySlugJoinedWithStagesAndAlternatives(string $slug): ?Event
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('e, s, a, u')
            ->from(Event::class, 'e')
            ->leftJoin('e.stages', 's')
            ->leftJoin('s.alternatives', 'a')
            ->leftJoin('s.preparers', 'u')
            ->where('e.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        /* @phpstan-ignore-next-line */
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneBySlugJoinedToAllChildEntities(string $slug): ?Event
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('e, s, sr, r, u, c')
            ->from(Event::class, 'e')
            ->leftJoin('e.stages', 's')
            ->leftJoin('s.stagesRegistrations', 'sr')
            ->leftJoin('sr.registration', 'r')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.companions', 'c')
            ->where('e.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        /* @phpstan-ignore-next-line */
        return $qb->getQuery()->getOneOrNullResult();
    }
}
