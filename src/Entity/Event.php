<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity]
#[ORM\Table(name: '`event`')]
#[UniqueEntity(fields: ['name'], message: 'Il y a déjà  un évènement portant ce nom !')]
class Event
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    public readonly UuidV4 $id;

    #[ORM\Column]
    public string $name;

    #[ORM\Column(type: 'text')]
    public string $description;

    #[ORM\Column]
    public readonly \DateTimeImmutable $createdAt;

    public function __construct(string $name, string $description)
    {
        $this->id = new UuidV4();
        $this->name = $name;
        $this->description = $description;
        $this->createdAt = new \DateTimeImmutable();
    }
}
