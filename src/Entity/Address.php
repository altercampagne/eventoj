<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Address
{
    #[Assert\NotBlank]
    #[ORM\Column]
    private string $addressLine1;

    #[ORM\Column(nullable: true)]
    private ?string $addressLine2 = null;

    #[Assert\NotBlank]
    #[ORM\Column]
    private string $city;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 6)]
    private string $zipCode;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 2)]
    private string $countryCode = 'FR';

    public function __toString(): string
    {
        $addressLine = $this->addressLine1;
        if (null !== $this->addressLine2) {
            $addressLine .= " {$this->addressLine2}";
        }

        return sprintf('%s, %s %s', $addressLine, $this->zipCode, $this->city);
    }

    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }
}
