<?php

declare(strict_types=1);

namespace App\Entity;

trait PersonTrait
{
    public function isChild(): bool
    {
        return (new \DateTimeImmutable())->diff($this->birthDate)->y < 13;
    }

    public function isAdult(): bool
    {
        return (new \DateTimeImmutable())->diff($this->birthDate)->y >= 18;
    }

    public function getFullName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }
}
