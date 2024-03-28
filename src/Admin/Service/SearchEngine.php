<?php

declare(strict_types=1);

namespace App\Admin\Service;

use App\Entity\Companion;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SearchEngine
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function search(string $query): SearchResult
    {
        $query = mb_strtolower("%{$query}%");

        return new SearchResult(
            $this->findUsers($query),
            $this->findCompanions($query),
            $this->findEvents($query),
        );
    }

    /**
     * @return User[]
     */
    private function findUsers(string $query): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('u')
            ->from(User::class, 'u')
            ->where('LOWER(u.email) LIKE :query OR LOWER(u.firstName) LIKE :query OR LOWER(u.lastName) LIKE :query')
            ->setParameter('query', $query)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Companion[]
     */
    private function findCompanions(string $query): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('c, u')
            ->from(Companion::class, 'c')
            ->leftJoin('c.user', 'u')
            ->where('LOWER(c.email) LIKE :query OR LOWER(c.firstName) LIKE :query OR LOWER(c.lastName) LIKE :query')
            ->setParameter('query', $query)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Event[]
     */
    private function findEvents(string $query): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('e')
            ->from(Event::class, 'e')
            ->where('LOWER(e.name) LIKE :query')
            ->setParameter('query', $query)
        ;

        return $qb->getQuery()->getResult();
    }
}
