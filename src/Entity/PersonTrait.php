<?php

declare(strict_types=1);

namespace App\Entity;

trait PersonTrait
{
    public function isChild(?\DateTimeInterface $atDate = null): bool
    {
        return $this->getAge($atDate) < 13;
    }

    public function isAdult(?\DateTimeInterface $atDate = null): bool
    {
        return $this->getAge($atDate) >= 18;
    }

    public function getAge(?\DateTimeInterface $atDate = null): int
    {
        if (null === $atDate) {
            $atDate = new \DateTimeImmutable();
        }

        return $atDate->diff($this->birthDate)->y;
    }

    public function getFullName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function getPublicName(): string
    {
        return $this->firstName.' '.mb_substr($this->lastName, 0, 1);
    }

    public function getDiet(): ?Diet
    {
        return $this->diet;
    }

    public function isLactoseIntolerant(): bool
    {
        return $this->lactoseIntolerant;
    }

    public function isGlutenIntolerant(): bool
    {
        return $this->glutenIntolerant;
    }

    public function getDietDetails(): ?string
    {
        return $this->dietDetails;
    }
}
