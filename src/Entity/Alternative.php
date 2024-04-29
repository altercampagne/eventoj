<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: '`alternative`')]
#[UniqueEntity(fields: ['slug'], message: 'Il y a déjà une alternative avec ce slug.')]
#[ORM\Index(name: 'idx_alternative_slug', fields: ['slug'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Alternative implements LocatedEntityInterface
{
    /**
     * This property should be marked as readonly but is not due to a bug in Doctrine.
     *
     * @see https://github.com/doctrine/orm/issues/9863
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private UuidV4 $id;

    #[Assert\NotNull]
    #[ORM\Column]
    private string $name;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private string $slug;

    #[Assert\Url]
    #[ORM\Column(nullable: true)]
    private ?string $website = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[Assert\Valid]
    #[ORM\Embedded(class: Address::class)]
    private Address $address;

    /**
     * @var Station[]
     */
    #[ORM\Column(type: 'json_document', options: ['jsonb' => true])]
    private array $stations = [];

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\OneToOne(targetEntity: UploadedFile::class)]
    #[ORM\JoinColumn(name: 'uploaded_file_id', referencedColumnName: 'id')]
    private ?UploadedFile $picture = null;

    /**
     * @var Collection<int, Stage>
     */
    #[ORM\ManyToMany(targetEntity: Stage::class, mappedBy: 'alternatives')]
    private Collection $stages;

    public function __construct()
    {
        $this->id = new UuidV4();
        $this->createdAt = new \DateTimeImmutable();
        $this->stages = new ArrayCollection();
    }

    public function isUsed(): bool
    {
        return 0 !== \count($this->stages);
    }

    /**
     * @return Event[]
     */
    public function getEvents(): array
    {
        $events = [];
        foreach ($this->stages as $stage) {
            $events[] = $stage->getEvent();
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

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
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

    /**
     * @return Station[]
     */
    public function getStations(): array
    {
        return $this->stations;
    }

    /**
     * This method contains a hack to ensure nested properties are saved when
     * updated! For this reason, we MUST use `by_reference = false` when
     * embedding stations in a FormType.
     *
     * @param Station[] $stations
     *
     * @see https://github.com/dunglas/doctrine-json-odm?tab=readme-ov-file#limitations-when-updating-nested-properties
     */
    public function setStations(array $stations): self
    {
        $clonedStations = [];
        foreach ($stations as $station) {
            $clonedStations[] = clone $station;
        }

        $this->stations = $clonedStations;

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

    public function getPicture(): ?UploadedFile
    {
        return $this->picture;
    }

    public function setPicture(?UploadedFile $picture): self
    {
        $this->picture = $picture;

        return $this;
    }
}
