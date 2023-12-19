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
#[ORM\Table(name: '`event`')]
#[UniqueEntity(fields: ['slug'], message: 'Il y a déjà un évènement avec ce slug.')]
class Event
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private readonly UuidV4 $id;

    #[ORM\Column(type: 'string', length: 3, enumType: EventType::class, options: [
        'comment' => 'Type of event (AT, BT, ADT, EB)',
    ])]
    private readonly EventType $type;

    #[ORM\Column]
    private string $name;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private string $slug;

    #[ORM\Column]
    private int $adultsCapacity;

    #[ORM\Column]
    private int $childrenCapacity;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: [
        'comment' => 'At which date members will be able to register to this event?',
    ])]
    private \DateTimeImmutable $openingDateForBookings;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private readonly \DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, Stage>
     */
    #[ORM\OneToMany(targetEntity: Stage::class, mappedBy: 'event')]
    private Collection $stages;

    private function __construct(EventType $type)
    {
        $this->id = new UuidV4();
        $this->type = $type;
        $this->createdAt = new \DateTimeImmutable();
        $this->stages = new ArrayCollection();
    }

    public static function AT(): self
    {
        $event = new self(EventType::AT);
        $event->adultsCapacity = 60;
        $event->childrenCapacity = 12;

        return $event;
    }

    public static function ADT(): self
    {
        $event = new self(EventType::ADT);
        $event->adultsCapacity = 40;
        $event->childrenCapacity = 8;

        return $event;
    }

    public static function BT(): self
    {
        $event = new self(EventType::BT);
        $event->adultsCapacity = 40;
        $event->childrenCapacity = 8;

        return $event;
    }

    public static function EB(): self
    {
        $event = new self(EventType::EB);
        $event->adultsCapacity = 30;
        $event->childrenCapacity = 6;

        return $event;
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getType(): EventType
    {
        return $this->type;
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getOpeningDateForBookings(): \DateTimeImmutable
    {
        return $this->openingDateForBookings;
    }

    public function setOpeningDateForBookings(\DateTimeImmutable $openingDateForBookings): self
    {
        $this->openingDateForBookings = $openingDateForBookings;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
