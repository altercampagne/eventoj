<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: '`event`')]
#[ORM\Index(name: 'idx_event_slug', fields: ['slug'])]
#[UniqueEntity(fields: ['slug'], message: 'Il y a déjà un évènement avec ce slug.')]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
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

    #[ORM\Column]
    private int $bikesAvailable;

    #[ORM\Column(options: [
        'comment' => 'The average price per day at which we need to sell tickets in order to break even on this event',
    ])]
    private int $breakEvenPricePerDay;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(options: [
        'comment' => 'At which date members will be able to register to this event?',
    ])]
    private \DateTimeImmutable $openingDateForBookings;

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
    #[ORM\OneToMany(targetEntity: Stage::class, mappedBy: 'event', cascade: ['persist'])]
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
        $this->openingDateForBookings = new \DateTimeImmutable('+6 months');
        $this->breakEvenPricePerDay = 3400;
    }

    public static function createFromType(EventType $type): self
    {
        return match ($type) {
            EventType::AT => self::AT(),
            EventType::ADT => self::ADT(),
            EventType::BT => self::BT(),
            EventType::EB => self::EB(),
        };
    }

    public static function AT(): self
    {
        $event = new self(EventType::AT);
        $event->adultsCapacity = 60;
        $event->childrenCapacity = 12;
        $event->bikesAvailable = 22;

        return $event;
    }

    public static function ADT(): self
    {
        $event = new self(EventType::ADT);
        $event->adultsCapacity = 40;
        $event->childrenCapacity = 8;
        $event->bikesAvailable = 0;

        return $event;
    }

    public static function BT(): self
    {
        $event = new self(EventType::BT);
        $event->adultsCapacity = 40;
        $event->childrenCapacity = 8;
        $event->bikesAvailable = 0;

        return $event;
    }

    public static function EB(): self
    {
        $event = new self(EventType::EB);
        $event->adultsCapacity = 30;
        $event->childrenCapacity = 6;
        $event->bikesAvailable = 0;

        return $event;
    }

    public function getAdultsCapacity(): int
    {
        return $this->adultsCapacity;
    }

    public function setAdultsCapacity(int $adultsCapacity): self
    {
        $this->adultsCapacity = $adultsCapacity;

        return $this;
    }

    public function getChildrenCapacity(): int
    {
        return $this->childrenCapacity;
    }

    public function setChildrenCapacity(int $childrenCapacity): self
    {
        $this->childrenCapacity = $childrenCapacity;

        return $this;
    }

    public function getBikesAvailable(): int
    {
        return $this->bikesAvailable;
    }

    public function setBikesAvailable(int $bikesAvailable): self
    {
        $this->bikesAvailable = $bikesAvailable;

        return $this;
    }

    public function getBreakEvenPricePerDay(): int
    {
        return $this->breakEvenPricePerDay;
    }

    public function setBreakEvenPricePerDay(int $breakEvenPricePerDay): self
    {
        $this->breakEvenPricePerDay = $breakEvenPricePerDay;

        return $this;
    }

    public function isFinished(): bool
    {
        if (null === $stage = $this->getLastStage()) {
            return false;
        }

        return new \DateTimeImmutable() > $stage->getDate();
    }

    public function isPublished(): bool
    {
        return null !== $this->publishedAt;
    }

    public function isBookable(): bool
    {
        if (!$this->isPublished()) {
            return false;
        }

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

    public function getPicture(): ?UploadedFile
    {
        return $this->picture;
    }

    public function setPicture(?UploadedFile $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        if ($this->isPublished()) {
            throw new \LogicException('Event is already published !');
        }

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Stage>
     */
    public function getStages(): Collection
    {
        return $this->stages;
    }

    public function getFirstStage(): ?Stage
    {
        if (false === $stage = $this->stages->first()) {
            return null;
        }

        return $stage;
    }

    public function getLastStage(): ?Stage
    {
        if (false === $stage = $this->stages->last()) {
            return null;
        }

        return $stage;
    }

    public function getNextComingStage(): ?Stage
    {
        $now = new \DateTimeImmutable();
        foreach ($this->stages as $stage) {
            if ($stage->getDate() > $now) {
                return $stage;
            }
        }

        return null;
    }

    public function addStage(Stage $stage): self
    {
        $this->stages->add($stage);

        return $this;
    }

    public function getPreviousStage(Stage $stage): ?Stage
    {
        if (false === $index = $this->stages->indexOf($stage)) {
            throw new \LogicException('Given stage does not belong to this event!');
        }

        if (0 === $index) {
            return null;
        }

        return $this->stages->get(--$index);
    }

    public function getNextStage(Stage $stage): ?Stage
    {
        if (false === $index = $this->stages->indexOf($stage)) {
            throw new \LogicException('Given stage does not belong to this event!');
        }

        return $this->stages->get(++$index);
    }

    /**
     * @return Alternative[]
     */
    public function getAlternatives(): array
    {
        $alternatives = [];
        foreach ($this->stages as $stage) {
            foreach ($stage->getStagesAlternatives() as $stageAlternative) {
                $alternatives[] = $stageAlternative->getAlternative();
            }
        }

        return array_unique($alternatives, \SORT_REGULAR);
    }

    /**
     * @return Collection<int, Registration>
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }
}
