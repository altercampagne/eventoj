<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Stage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stage>
 */
class StageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stage::class);
    }

    public function findOneBySlugJoinedToAllChildEntities(string $slug): ?Stage
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('s, e, sr, r, u, c')
            ->from(Stage::class, 's')
            ->leftJoin('s.event', 'e')
            ->leftJoin('s.stagesRegistrations', 'sr')
            ->leftJoin('sr.registration', 'r')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.companions', 'c')
            ->where('s.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        /* @phpstan-ignore-next-line */
        return $qb->getQuery()->getOneOrNullResult();
    }
}
