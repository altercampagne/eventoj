<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity]
#[ORM\Table(name: '`stage`')]
#[UniqueEntity(fields: ['event.id', 'slug'], message: 'Il y a déjà une étape avec ce slug pour cet évènement.')]
class Stage
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private readonly UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'stages')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id')]
    private readonly Event $event;

    #[ORM\Column(type: 'string', enumType: StageType::class)]
    private StageType $type;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $date;

    #[ORM\Column]
    private string $name;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private string $slug;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'string', enumType: StageDifficulty::class)]
    private StageDifficulty $difficulty;

    #[ORM\ManyToOne(targetEntity: Alternative::class)]
    #[ORM\JoinColumn(name: 'departure_alternative_id', referencedColumnName: 'id')]
    private ?Alternative $departureAlternative = null;

    #[ORM\ManyToOne(targetEntity: Alternative::class)]
    #[ORM\JoinColumn(name: 'arrival_alternative_id', referencedColumnName: 'id')]
    private ?Alternative $arrivalAlternative = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private readonly \DateTimeImmutable $createdAt;

    public function __construct(Event $event)
    {
        $this->id = new UuidV4();
        $this->event = $event;
        $this->type = StageType::CLASSIC;
        $this->difficulty = StageDifficulty::MEDIUM;
        $this->createdAt = new \DateTimeImmutable();
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

    public function getDifficulty(): StageDifficulty
    {
        return $this->difficulty;
    }

    public function setDifficulty(StageDifficulty $difficulty): self
    {
        $this->difficulty = $difficulty;

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

    public function getDepartureAlternative(): ?Alternative
    {
        return $this->departureAlternative;
    }

    public function setDepartureAlternative(?Alternative $departureAlternative): self
    {
        $this->departureAlternative = $departureAlternative;

        return $this;
    }

    public function getArrivalAlternative(): ?Alternative
    {
        return $this->arrivalAlternative;
    }

    public function setArrivalAlternative(?Alternative $arrivalAlternative): self
    {
        $this->arrivalAlternative = $arrivalAlternative;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
