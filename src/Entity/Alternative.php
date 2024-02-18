<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AlternativeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity(repositoryClass: AlternativeRepository::class)]
#[ORM\Table(name: '`alternative`')]
#[UniqueEntity(fields: ['slug'], message: 'Il y a déjà une alternative avec ce slug.')]
#[ORM\Index(name: 'idx_alternative_slug', fields: ['slug'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Alternative
{
    /**
     * This property should be marked as readonly but is not due to a bug in Doctrine.
     *
     * @see https://github.com/doctrine/orm/issues/9863
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private UuidV4 $id;

    #[ORM\Column]
    private string $name;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private string $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Embedded(class: Address::class)]
    private Address $address;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt;

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

    /**
     * @return Event[]
     */
    public function getEvents(): array
    {
        $events = [];
        foreach ($this->stagesAlternatives as $stageAlternative) {
            $events[] = $stageAlternative->getStage()->getEvent();
        }

        return array_unique($events, \SORT_REGULAR);
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, StageAlternative>
     */
    public function getStagesAlternatives(): Collection
    {
        return $this->stagesAlternatives;
    }
}
