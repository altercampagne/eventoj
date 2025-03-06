<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Document\EventPicture;
use App\Entity\Document\UploadedImage;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

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

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 3, enumType: EventType::class, options: [
        'comment' => 'Type of event (AT, BT, ADT, EB)',
    ])]
    private readonly EventType $type;

    #[Assert\NotBlank]
    #[ORM\Column]
    private string $name;

    #[ORM\Column(nullable: true, options: [
        'comment' => 'ID of the Paheko project to which all transactions will ba attached.',
    ])]
    private ?string $pahekoProjectId = null;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = null;

    #[Assert\NotBlank]
    #[ORM\Column]
    private int $adultsCapacity;

    #[Assert\NotBlank]
    #[ORM\Column]
    private int $childrenCapacity;

    #[Assert\NotBlank]
    #[ORM\Column]
    private int $bikesAvailable;

    #[Assert\NotBlank]
    #[ORM\Column(options: [
        'comment' => 'The minimum price per day',
    ])]
    private int $minimumPricePerDay = 2000;

    #[Assert\NotBlank]
    #[ORM\Column(options: [
        'comment' => 'The average price per day at which we need to sell tickets in order to break even on this event',
    ])]
    private int $breakEvenPricePerDay = 3300;

    #[Assert\NotBlank]
    #[ORM\Column(options: [
        'comment' => 'The suggested support price per day for this event',
    ])]
    private int $supportPricePerDay = 4700;

    #[Assert\NotBlank]
    #[ORM\Column(options: [
        'comment' => 'The maximum number of days at the solidarity price.',
    ])]
    private int $daysAtSolidarityPrice = 8;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[Assert\NotBlank]
    #[ORM\Column(options: [
        'comment' => 'At which date members will be able to register to this event?',
    ])]
    private \DateTimeImmutable $openingDateForBookings;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(targetEntity: UploadedImage::class)]
    #[ORM\JoinColumn(name: 'uploaded_file_id', referencedColumnName: 'id')]
    private ?UploadedImage $picture = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 10, enumType: Meal::class)]
    private Meal $firstMealOfFirstDay = Meal::LUNCH;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 10, enumType: Meal::class)]
    private Meal $lastMealOfLastDay = Meal::LUNCH;

    #[Assert\Url(requireTld: true)]
    #[ORM\Column(nullable: true)]
    private ?string $exchangeMarketLink = null;

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

    /**
     * @var Collection<int, EventPicture>
     */
    #[ORM\OneToMany(targetEntity: EventPicture::class, mappedBy: 'event')]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $memberUploadedPictures;

    private function __construct(EventType $type)
    {
        $this->id = new UuidV4();
        $this->type = $type;
        $this->createdAt = new \DateTimeImmutable();
        $this->stages = new ArrayCollection();
        $this->registrations = new ArrayCollection();
        $this->memberUploadedPictures = new ArrayCollection();
        $this->openingDateForBookings = new \DateTimeImmutable('+6 months');
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
        $event->childrenCapacity = 8;
        $event->bikesAvailable = 21;

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

    public function isAT(): bool
    {
        return EventType::AT === $this->type;
    }

    public function isEB(): bool
    {
        return EventType::EB === $this->type;
    }

    public function isFull(): bool
    {
        if (!$this->isAT()) {
            if (null === $stage = $this->getFirstStage()) {
                return false;
            }

            return $stage->isFull();
        }

        foreach ($this->stages as $stage) {
            if (!$stage->isFull()) {
                return false;
            }
        }

        return true;
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

    public function getMinimumPricePerDay(): int
    {
        return $this->minimumPricePerDay;
    }

    public function setMinimumPricePerDay(int $minimumPricePerDay): self
    {
        $this->minimumPricePerDay = $minimumPricePerDay;

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

    public function getSupportPricePerDay(): int
    {
        return $this->supportPricePerDay;
    }

    public function setSupportPricePerDay(int $supportPricePerDay): self
    {
        $this->supportPricePerDay = $supportPricePerDay;

        return $this;
    }

    public function getDaysAtSolidarityPrice(): int
    {
        return $this->daysAtSolidarityPrice;
    }

    public function setDaysAtSolidarityPrice(int $daysAtSolidarityPrice): self
    {
        $this->daysAtSolidarityPrice = $daysAtSolidarityPrice;

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

    public function getPahekoProjectId(): ?string
    {
        return $this->pahekoProjectId;
    }

    public function setPahekoProjectId(?string $pahekoProjectId): self
    {
        $this->pahekoProjectId = $pahekoProjectId;

        return $this;
    }

    public function getSlug(): ?string
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

    public function getPicture(): ?UploadedImage
    {
        return $this->picture;
    }

    public function setPicture(?UploadedImage $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getFirstMealOfFirstDay(): Meal
    {
        return $this->firstMealOfFirstDay;
    }

    public function firstDayIncludesBreakfast(): bool
    {
        return Meal::BREAKFAST === $this->firstMealOfFirstDay;
    }

    public function firstDayIncludesLunch(): bool
    {
        return Meal::DINNER !== $this->firstMealOfFirstDay;
    }

    public function lastDayIncludesLunch(): bool
    {
        return Meal::BREAKFAST !== $this->lastMealOfLastDay;
    }

    public function lastDayIncludesDinner(): bool
    {
        return Meal::DINNER === $this->lastMealOfLastDay;
    }

    public function setFirstMealOfFirstDay(Meal $firstMealOfFirstDay): self
    {
        $this->firstMealOfFirstDay = $firstMealOfFirstDay;

        return $this;
    }

    public function getLastMealOfLastDay(): Meal
    {
        return $this->lastMealOfLastDay;
    }

    public function setLastMealOfLastDay(Meal $lastMealOfLastDay): self
    {
        $this->lastMealOfLastDay = $lastMealOfLastDay;

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

    public function getExchangeMarketLink(): ?string
    {
        return $this->exchangeMarketLink;
    }

    public function setExchangeMarketLink(?string $exchangeMarketLink): self
    {
        $this->exchangeMarketLink = $exchangeMarketLink;

        return $this;
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

        $index = (int) $index;
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

        $index = (int) $index;

        return $this->stages->get(++$index);
    }

    /**
     * @return Alternative[]
     */
    public function getAlternatives(): array
    {
        $alternatives = [];
        foreach ($this->stages as $stage) {
            $alternatives = array_merge($alternatives, $stage->getAlternatives()->getValues());
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

    /**
     * @return Collection<int, Registration>
     */
    public function getConfirmedRegistrations(): Collection
    {
        return $this->registrations->filter(static fn (Registration $registration): bool => $registration->isConfirmed());
    }

    public function countPeople(): int
    {
        $people = [];
        $this->getConfirmedRegistrations()->map(static function (Registration $registration) use (&$people): void {
            foreach ($registration->getPeople() as $person) {
                $people[] = (string) $person->getId();
            }
        });

        $people = array_unique($people);

        return \count($people);
    }

    /**
     * @return string[]
     */
    public function getRegisteredPeopleEmails(): array
    {
        $mails = [];

        foreach ($this->getConfirmedRegistrations() as $registration) {
            foreach ($registration->getPeople() as $person) {
                $mails[] = $person->getEmail();
            }
        }

        return array_unique($mails);
    }

    /**
     * @return Collection<int, EventPicture>
     */
    public function getMemberUploadedPictures(): Collection
    {
        return $this->memberUploadedPictures;
    }
}
