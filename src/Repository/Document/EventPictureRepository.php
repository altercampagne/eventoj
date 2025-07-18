<?php

declare(strict_types=1);

namespace App\Repository\Document;

use App\Entity\Document\EventPicture;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventPicture>
 */
class EventPictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventPicture::class);
    }

    /**
     * @return EventPicture[]
     */
    public function findByUserAndEvent(User $user, Event $event): array
    {
        return $this->findBy(['user' => $user, 'event' => $event], ['createdAt' => 'ASC']);
    }

    /**
     * Returns all event pictures which must be checked.
     * This means: all recently uploaded pictures not already checked on remote
     * storage. Very recent pictures are not returned cause they might be
     * currently uploading.
     *
     * @return EventPicture[]
     */
    public function findToCheck(): array
    {
        $qb = $this->createQueryBuilder('e');
        $qb
            ->where('e.existsOnRemoteStorageAt is null')
            ->andWhere('e.createdAt < :created_before_date')
            ->setParameter('created_before_date', new \DateTimeImmutable('-30 minutes'))
        ;

        return $qb->getQuery()->getResult();
    }
}
