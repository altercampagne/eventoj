<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Alternative;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alternative>
 *
 * @method Alternative|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alternative|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alternative[]    findAll()
 * @method Alternative[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlternativeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alternative::class);
    }

    /**
     * @return Alternative[]
     */
    public function findAllJoinedToEvents(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('a, sa, s, e')
            ->from(Alternative::class, 'a')
            ->leftJoin('a.stagesAlternatives', 'sa')
            ->leftJoin('sa.stage', 's')
            ->leftJoin('s.event', 'e')
            ->orderBy('a.name', 'ASC')
        ;

        return $qb->getQuery()->getResult();
    }
}
