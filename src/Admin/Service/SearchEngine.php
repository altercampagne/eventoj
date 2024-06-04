<?php

declare(strict_types=1);

namespace App\Admin\Service;

use App\Admin\Security\Permission;
use App\Entity\Alternative;
use App\Entity\Companion;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class SearchEngine
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    ) {
    }

    public function search(string $query): SearchResult
    {
        $query = mb_strtolower("%{$query}%");

        return new SearchResult(
            $this->findUsers($query),
            $this->findCompanions($query),
            $this->findEvents($query),
            $this->findAlternatives($query),
        );
    }

    /**
     * @return User[]
     */
    private function findUsers(string $query): array
    {
        if (!$this->security->isGranted(Permission::USER_LIST->value)) {
            return [];
        }

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('u')
            ->from(User::class, 'u')
            ->where('LOWER(u.email) LIKE :query OR LOWER(UNACCENT(u.firstName)) LIKE UNACCENT(:query) OR LOWER(UNACCENT(u.firstName)) LIKE UNACCENT(:query) OR UNACCENT(LOWER(u.lastName)) LIKE UNACCENT(:query) OR UNACCENT(LOWER(u.publicName)) LIKE UNACCENT(:query)')
            ->setParameter('query', $query)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Companion[]
     */
    private function findCompanions(string $query): array
    {
        if (!$this->security->isGranted(Permission::USER_LIST->value)) {
            return [];
        }

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('c, u')
            ->from(Companion::class, 'c')
            ->leftJoin('c.user', 'u')
            ->where('LOWER(c.email) LIKE :query OR LOWER(UNACCENT(c.firstName)) LIKE UNACCENT(:query) OR LOWER(UNACCENT(c.lastName)) LIKE UNACCENT(:query)')
            ->setParameter('query', $query)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Event[]
     */
    private function findEvents(string $query): array
    {
        if (!$this->security->isGranted(Permission::EVENT_LIST->value)) {
            return [];
        }

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('e')
            ->from(Event::class, 'e')
            ->where('LOWER(UNACCENT(e.name)) LIKE UNACCENT(:query)')
            ->setParameter('query', $query)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Alternative[]
     */
    private function findAlternatives(string $query): array
    {
        if (!$this->security->isGranted(Permission::ALTERNATIVE_LIST->value)) {
            return [];
        }

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('a')
            ->from(Alternative::class, 'a')
            ->where('LOWER(UNACCENT(a.name)) LIKE UNACCENT(:query)')
            ->setParameter('query', $query)
        ;

        return $qb->getQuery()->getResult();
    }
}
