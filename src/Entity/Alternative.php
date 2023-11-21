<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity]
#[ORM\Table(name: '`alternative`')]
class Alternative
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
