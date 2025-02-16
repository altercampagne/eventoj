<?php

declare(strict_types=1);

namespace App\Entity\Document;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\Document\EventPictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: EventPictureRepository::class)]
#[ORM\Table(name: '`event_picture`')]
#[UniqueEntity(fields: ['path'], message: 'Il y a déjà un fichier avec ce chemin.')]
#[ORM\Index(name: 'idx_event_picture_path', fields: ['path'])]
class EventPicture extends AbstractUploadedImage
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private readonly User $user;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'memberUploadedPictures')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: false)]
    private readonly Event $event;

    public function __construct(
        User $user,
        Event $event,
        string $path,
        string $originalFileName,
        ?int $width = null,
        ?int $height = null,
    ) {
        parent::__construct($path, $originalFileName, $width, $height);

        $this->user = $user;
        $this->event = $event;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }
}
