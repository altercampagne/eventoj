<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: '`stages_registrations`')]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class StageRegistration
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private readonly Uuid $id;

    #[ORM\ManyToOne(targetEntity: Stage::class, inversedBy: 'stagesRegistrations')]
    #[ORM\JoinColumn(name: 'stage_id', referencedColumnName: 'id', nullable: false)]
    private Stage $stage;

    #[ORM\ManyToOne(targetEntity: Registration::class, inversedBy: 'stagesRegistrations')]
    #[ORM\JoinColumn(name: 'registration_id', referencedColumnName: 'id', nullable: false)]
    private Registration $registration;

    #[ORM\Column(type: Types::BOOLEAN, options: [
        'comment' => 'Does the participant will be present for the breakfast?',
    ])]
    private bool $presentForBreakfast = true;

    #[ORM\Column(type: Types::BOOLEAN, options: [
        'comment' => 'Does the participant will be present for the lunch?',
    ])]
    private bool $presentForLunch = true;

    #[ORM\Column(type: Types::BOOLEAN, options: [
        'comment' => 'Does the participant will be present for the dinner?',
    ])]
    private bool $presentForDinner = true;

    public function __construct(Stage $stage, Registration $registration)
    {
        $this->id = Uuid::v7();
        $this->stage = $stage;
        $this->registration = $registration;
    }

    public function getFirstMeal(): Meal
    {
        if ($this->presentForBreakfast()) {
            return Meal::BREAKFAST;
        }

        if ($this->presentForLunch()) {
            return Meal::LUNCH;
        }

        return Meal::DINNER;
    }

    public function getLastMeal(): Meal
    {
        if ($this->presentForDinner()) {
            return Meal::DINNER;
        }

        if ($this->presentForLunch()) {
            return Meal::LUNCH;
        }

        return Meal::BREAKFAST;
    }

    public function includesMeal(Meal $meal): bool
    {
        return match ($meal) {
            Meal::BREAKFAST => $this->presentForBreakfast(),
            Meal::LUNCH => $this->presentForLunch(),
            Meal::DINNER => $this->presentForDinner(),
        };
    }

    public function getId(): Uuid
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

    public function getRegistration(): Registration
    {
        return $this->registration;
    }

    public function setRegistration(Registration $registration): self
    {
        $this->registration = $registration;

        return $this;
    }

    public function presentForBreakfast(): bool
    {
        return $this->presentForBreakfast;
    }

    public function setPresentForBreakfast(bool $presentForBreakfast): self
    {
        $this->presentForBreakfast = $presentForBreakfast;

        return $this;
    }

    public function presentForLunch(): bool
    {
        return $this->presentForLunch;
    }

    public function setPresentForLunch(bool $presentForLunch): self
    {
        $this->presentForLunch = $presentForLunch;

        return $this;
    }

    public function presentForDinner(): bool
    {
        return $this->presentForDinner;
    }

    public function setPresentForDinner(bool $presentForDinner): self
    {
        $this->presentForDinner = $presentForDinner;

        return $this;
    }
}
