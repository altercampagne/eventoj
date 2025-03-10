<?php

declare(strict_types=1);

namespace App\Entity;

use App\Bridge\Helloasso\Validator\HelloassoName;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'companion')]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Companion
{
    use PersonTrait;

    /**
     * This property should be marked as readonly but is not due to a bug in Doctrine.
     *
     * @see https://github.com/doctrine/orm/issues/9863
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private UuidV4 $id;

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
    #[Assert\LessThan('now', message: 'Une date de naissance dans le futur, ça ne va pas être possible !')]
    #[Assert\GreaterThan('-125 years', message: 'Une vraie date de naissance, ce serait mieux ! :)')]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $birthDate;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 180)]
    private string $email;

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

    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: true, options: [
        'comment' => 'Free field to provide more information about user diet.',
    ])]
    private ?string $dietDetails = null;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Registration>
     */
    #[ORM\ManyToMany(targetEntity: Registration::class, mappedBy: 'companions')]
    private Collection $registrations;

    /**
     * @var Collection<int, Membership>
     */
    #[ORM\OneToMany(targetEntity: Membership::class, mappedBy: 'companion', cascade: ['persist'])]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $memberships;

    public function __construct(User $user)
    {
        $this->id = new UuidV4();
        $this->user = $user;
        $this->createdAt = new \DateTimeImmutable();
        $this->registrations = new ArrayCollection();
        $this->memberships = new ArrayCollection();
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
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

    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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

    public function setDiet(Diet $diet): self
    {
        $this->diet = $diet;

        return $this;
    }

    public function setGlutenIntolerant(bool $glutenIntolerant): self
    {
        $this->glutenIntolerant = $glutenIntolerant;

        return $this;
    }

    public function setLactoseIntolerant(bool $lactoseIntolerant): self
    {
        $this->lactoseIntolerant = $lactoseIntolerant;

        return $this;
    }

    public function setDietDetails(?string $dietDetails): self
    {
        $this->dietDetails = $dietDetails;

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
     * @return Collection<int, Registration>
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    /**
     * @return Collection<int, Membership>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function getLatestMembership(): ?Membership
    {
        if (false === $membership = $this->memberships->last()) {
            return null;
        }

        return $membership;
    }

    public function addMembership(Membership $membership): void
    {
        $this->memberships->add($membership);
    }
}
