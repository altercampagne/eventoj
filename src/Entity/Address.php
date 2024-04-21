<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use ZipCodeValidator\Constraints\ZipCode;

#[ORM\Embeddable]
class Address
{
    #[ORM\Column(nullable: true)]
    private ?string $addressLine1 = null;

    #[ORM\Column(nullable: true)]
    private ?string $addressLine2 = null;

    #[ORM\Column]
    private string $city;

    #[ZipCode(['getter' => 'getCountryCode', 'message' => 'Ce code postal n\'est pas valide.'])]
    #[ORM\Column(type: Types::STRING, length: 6)]
    private string $zipCode;

    #[ORM\Column(type: Types::STRING, length: 2)]
    private ?string $countryCode = 'FR';

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    public function __toString(): string
    {
        $addressLine = $this->addressLine1 ?? '';
        if (null !== $this->addressLine2) {
            $addressLine .= " {$this->addressLine2}";
        }
        if (null !== $this->addressLine1 || null !== $this->addressLine2) {
            $addressLine .= ', ';
        }

        $address = $addressLine.$this->zipCode.' '.$this->city;

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

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(?string $addressLine1): self
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

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): self
    {
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

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, mixed $payload): void
    {
        if (null === $this->latitude) {
            $context->buildViolation('Tu dois sélectionner une adresse parmi celle proposées.')
                ->atPath('address')
                ->addViolation();
        }
    }
}
