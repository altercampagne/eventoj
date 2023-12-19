<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity]
#[ORM\Table(name: '`alternative`')]
#[UniqueEntity(fields: ['slug'], message: 'Il y a déjà une alternative avec ce slug.')]
class Alternative
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private readonly UuidV4 $id;

    #[ORM\Column]
    private string $name;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private string $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Embedded(class: Address::class)]
    private Address $address;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private readonly \DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, StageAlternative>
     */
    #[ORM\OneToMany(targetEntity: StageAlternative::class, mappedBy: 'alternative')]
    private Collection $stagesAlternatives;

    public function __construct()
    {
        $this->id = new UuidV4();
        $this->createdAt = new \DateTimeImmutable();
        $this->stagesAlternatives = new ArrayCollection();
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, StageAlternative>
     */
    public function getStagesAlternatives(): Collection
    {
        return $this->stagesAlternatives;
    }
}
