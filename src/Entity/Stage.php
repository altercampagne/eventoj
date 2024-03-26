<?php

declare(strict_types=1);

namespace App\Entity;

use App\Service\Availability\StageAvailability;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: '`stage`')]
#[UniqueEntity(fields: ['event', 'slug'], message: 'Il y a déjà une étape avec ce slug pour cet évènement.')]
#[ORM\Index(name: 'idx_stage_slug', fields: ['slug'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Stage
{
    /**
     * This property should be marked as readonly but is not due to a bug in Doctrine.
     *
     * @see https://github.com/doctrine/orm/issues/9863
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'stages')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: false)]
    private readonly Event $event;

    #[ORM\Column(type: 'string', length: 7, enumType: StageType::class, options: [
        'comment' => 'Type of this stage (before, after, classic).',
    ])]
    private StageType $type;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $date;

    #[Assert\NotBlank]
    #[ORM\Column]
    private string $name;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private string $slug;

    #[Assert\NotBlank(message: 'La description ne doit pas être vide.')]
    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'string', length: 6, nullable: true, enumType: StageDifficulty::class, options: [
        'comment' => 'Difficulty of this stage (easy, medium, hard).',
    ])]
    private ?StageDifficulty $difficulty = null;

    #[Assert\Url]
    #[ORM\Column(nullable: true, options: [
        'comment' => 'The URL of the route (komoot or openrunner) to embed on website',
    ])]
    private ?string $routeUrl = null;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, Alternative>
     */
    #[ORM\ManyToMany(targetEntity: Alternative::class, inversedBy: 'stages')]
    #[ORM\JoinTable(name: 'stages_alternatives')]
    private Collection $alternatives;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'preparedStages')]
    #[ORM\JoinTable(name: 'stages_preparations')]
    private Collection $preparers;

    /**
     * @var Collection<int, StageRegistration>
     */
    #[ORM\OneToMany(targetEntity: StageRegistration::class, mappedBy: 'stage')]
    private Collection $stagesRegistrations;

    public function __construct(Event $event)
    {
        $this->id = new UuidV4();
        $this->event = $event;
        $this->type = StageType::CLASSIC;
        $this->createdAt = new \DateTimeImmutable();
        $this->alternatives = new ArrayCollection();
        $this->stagesRegistrations = new ArrayCollection();
    }

    public function isBefore(): bool
    {
        return StageType::BEFORE === $this->type;
    }

    public function isAfter(): bool
    {
        return StageType::AFTER === $this->type;
    }

    public function isFree(): bool
    {
        return $this->isBefore() || $this->isAfter();
    }

    public function isDifficultyEasy(): bool
    {
        return StageDifficulty::EASY === $this->difficulty;
    }

    public function isDifficultyMedium(): bool
    {
        return StageDifficulty::MEDIUM === $this->difficulty;
    }

    public function isDifficultyHard(): bool
    {
        return StageDifficulty::HARD === $this->difficulty;
    }

    public function isOver(): bool
    {
        return $this->date < new \DateTimeImmutable();
    }

    public function getAvailability(): StageAvailability
    {
        return new StageAvailability($this);
    }

    public function isToday(): bool
    {
        return $this->date->format('Y-m-d') === (new \DateTimeImmutable())->format('Y-m-d');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getType(): StageType
    {
        return $this->type;
    }

    public function setType(StageType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDifficulty(): ?StageDifficulty
    {
        return $this->difficulty;
    }

    public function setDifficulty(?StageDifficulty $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getRouteUrl(): ?string
    {
        return $this->routeUrl;
    }

    public function setRouteUrl(?string $routeUrl): self
    {
        $this->routeUrl = $routeUrl;

        return $this;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Alternative>
     */
    public function getAlternatives(): Collection
    {
        return $this->alternatives;
    }

    /**
     * @param Collection<int, Alternative> $alternatives
     */
    public function setAlternatives(Collection $alternatives): self
    {
        $this->alternatives = $alternatives;

        return $this;
    }

    public function addAlternative(Alternative $alternative): self
    {
        $this->alternatives->add($alternative);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getPreparers(): Collection
    {
        return $this->preparers;
    }

    /**
     * @param Collection<int, User> $preparers
     */
    public function setPreparers(Collection $preparers): self
    {
        $this->preparers = $preparers;

        return $this;
    }

    public function addPreparer(User $preparer): self
    {
        $this->preparers->add($preparer);

        return $this;
    }

    /**
     * @return Collection<int, StageRegistration>
     */
    public function getStagesRegistrations(): Collection
    {
        return $this->stagesRegistrations;
    }

    /**
     * @return Collection<int, StageRegistration>
     */
    public function getConfirmedStagesRegistrations(): Collection
    {
        return $this->stagesRegistrations->filter(static function (StageRegistration $stageRegistration): bool {
            return $stageRegistration->getRegistration()->isConfirmed();
        });
    }
}
