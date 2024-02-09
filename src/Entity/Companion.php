<?php

declare(strict_types=1);

namespace App\Entity;

use App\Bridge\Helloasso\Validator\HelloassoName;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'companion')]
class Companion
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private readonly UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'companions')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private readonly User $user;

    #[Assert\NotBlank]
    #[HelloassoName]
    #[ORM\Column]
    private string $firstName;

    #[Assert\NotBlank]
    #[HelloassoName]
    #[ORM\Column]
    private string $lastName;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $birthDate;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[AssertPhoneNumber(regionPath: 'user.address.countryCode')]
    #[ORM\Column(type: 'phone_number', nullable: true)]
    private ?PhoneNumber $phoneNumber = null;

    #[ORM\Column(type: 'string', enumType: Diet::class, options: [
        'comment' => 'Diet of the user (omnivore, vegetarien, vegan)',
    ])]
    private Diet $diet;

    #[ORM\Column(type: 'boolean', options: [
        'default' => false,
    ])]
    private bool $glutenIntolerant = false;

    #[ORM\Column(type: 'boolean', options: [
        'default' => false,
    ])]
    private bool $lactoseIntolerant = false;

    #[ORM\Column(type: 'string', nullable: true, options: [
        'comment' => 'Free field to provide more information about user diet.',
    ])]
    private ?string $dietDetails = null;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    public function __construct(User $user)
    {
        $this->id = new UuidV4();
        $this->user = $user;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?PhoneNumber $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getDiet(): ?Diet
    {
        return $this->diet;
    }

    public function setDiet(Diet $diet): self
    {
        $this->diet = $diet;

        return $this;
    }

    public function isGlutenIntolerant(): bool
    {
        return $this->glutenIntolerant;
    }

    public function setGlutenIntolerant(bool $glutenIntolerant): self
    {
        $this->glutenIntolerant = $glutenIntolerant;

        return $this;
    }

    public function isLactoseIntolerant(): bool
    {
        return $this->lactoseIntolerant;
    }

    public function setLactoseIntolerant(bool $lactoseIntolerant): self
    {
        $this->lactoseIntolerant = $lactoseIntolerant;

        return $this;
    }

    public function getDietDetails(): ?string
    {
        return $this->dietDetails;
    }

    public function setDietDetails(?string $dietDetails): self
    {
        $this->dietDetails = $dietDetails;

        return $this;
    }
}
