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
}
