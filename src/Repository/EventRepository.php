<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findOneBySlugJoinedToAllChildEntities(string $slug): ?Event
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('e, s, sr, r, u')
            ->from(Event::class, 'e')
            ->innerJoin('e.stages', 's')
            ->leftJoin('s.stagesRegistrations', 'sr')
            ->leftJoin('sr.registration', 'r')
            ->leftJoin('r.user', 'u')
            ->where('e.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        /* @phpstan-ignore-next-line */
        return $qb->getQuery()->getOneOrNullResult();
    }
}
