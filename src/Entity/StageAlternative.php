<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity]
#[ORM\Table(name: '`stages_alternatives`')]
class StageAlternative
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private readonly UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: Stage::class, inversedBy: 'stagesAlternatives')]
    #[ORM\JoinColumn(name: 'stage_id', referencedColumnName: 'id', nullable: false)]
    private Stage $stage;

    #[ORM\ManyToOne(targetEntity: Alternative::class, inversedBy: 'stagesAlternatives')]
    #[ORM\JoinColumn(name: 'alternative_id', referencedColumnName: 'id', nullable: false)]
    private Alternative $alternative;

    #[ORM\Column(type: 'string', length: 10, enumType: StageAlternativeRelation::class, options: [
        'comment' => 'Relation between stage & alternative (departure, arrival, visit, full_day)',
    ])]
    private StageAlternativeRelation $relation;

    public function __construct()
    {
        $this->id = new UuidV4();
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getStage(): Stage
    {
        return $this->stage;
    }

    public function setStage(Stage $stage): self
    {
        $this->stage = $stage;

        return $this;
    }

    public function getAlternative(): Alternative
    {
        return $this->alternative;
    }

    public function setAlternative(Alternative $alternative): self
    {
        $this->alternative = $alternative;

        return $this;
    }

    public function getRelation(): StageAlternativeRelation
    {
        return $this->relation;
    }

    public function setRelation(StageAlternativeRelation $relation): self
    {
        $this->relation = $relation;

        return $this;
    }
}
