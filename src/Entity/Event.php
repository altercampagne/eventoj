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
    /**
     * This property should be marked as readonly but is not due to a bug in Doctrine.
     *
     * @see https://github.com/doctrine/orm/issues/9863
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private UuidV4 $id;

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
    #[ORM\OrderBy(['date' => 'ASC'])]
    private Collection $stages;

    /**
     * @var Collection<int, Registration>
     */
    #[ORM\OneToMany(targetEntity: Registration::class, mappedBy: 'event')]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $registrations;

    private function __construct(EventType $type)
    {
        $this->id = new UuidV4();
        $this->type = $type;
        $this->createdAt = new \DateTimeImmutable();
        $this->stages = new ArrayCollection();
        $this->registrations = new ArrayCollection();
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

    public function isFinished(): bool
    {
        if (null === $stage = $this->getLatestStage()) {
            return false;
        }

        return new \DateTimeImmutable() > $stage->getDate();
    }

    public function isBookable(): bool
    {
        $now = new \DateTimeImmutable();

        if ($now < $this->openingDateForBookings) {
            return false;
        }

        return !$this->isFinished();
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

    /**
     * @return Collection<int, Stage>
     */
    public function getStages(): Collection
    {
        return $this->stages;
    }

    public function getLatestStage(): ?Stage
    {
        if (false === $stage = $this->stages->last()) {
            return null;
        }

        return $stage;
    }

    /**
     * @return Collection<int, Registration>
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }
}
