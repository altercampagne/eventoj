<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ZipCodeValidator\Constraints\ZipCode;

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
    #[ZipCode(['getter' => 'getCountryCode', 'message' => 'Ce code postal n\'est pas valide.'])]
    #[ORM\Column(type: Types::STRING, length: 6)]
    private string $zipCode;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 2)]
    private string $countryCode = 'FR';

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    public function __toString(): string
    {
        $addressLine = $this->addressLine1;
        if (null !== $this->addressLine2) {
            $addressLine .= " {$this->addressLine2}";
        }

        $address = $addressLine.', '.$this->zipCode.' '.$this->city;

        if ('FR' === $this->countryCode) {
            return $address;
        }

        $country = match ($this->countryCode) {
            'BE' => 'Belgique',
            'CH' => 'Suisse',
            'DE' => 'Allemagne',
            'ES' => 'Espagne',
            'GB' => 'Royaume-Uni',
            'IT' => 'Italie',
            'LU' => 'Luxembourg',
            default => $this->countryCode,
        };

        return $address." - {$country}";
    }

    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;
        $this->latitude = null;
        $this->longitude = null;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;
        $this->latitude = null;
        $this->longitude = null;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        $this->latitude = null;
        $this->longitude = null;

        return $this;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;
        $this->latitude = null;
        $this->longitude = null;

        return $this;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->latitude = null;
        $this->longitude = null;
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }
}
