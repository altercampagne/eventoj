<?php

declare(strict_types=1);

namespace App\Entity;

use App\Bridge\Helloasso\Validator\HelloassoName;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte avec cette adresse mail')]
#[ORM\Index(name: 'idx_user_email', fields: ['email'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, LocatedEntityInterface
{
    use PersonTrait;

    /**
     * This property should be marked as readonly but is not due to a bug in Doctrine.
     *
     * @see https://github.com/doctrine/orm/issues/9863
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(unique: true, nullable: true)]
    private ?string $pahekoId = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[Assert\NotBlank]
    #[HelloassoName]
    #[ORM\Column]
    private string $firstName;

    #[Assert\NotBlank]
    #[HelloassoName]
    #[ORM\Column]
    private string $lastName;

    #[ORM\Column(nullable: true)]
    private ?string $publicName = null;

    #[Assert\NotBlank]
    #[Assert\LessThan('-18 years', message: 'Tu dois être majeur pour pouvoir t\'inscrire.')]
    #[Assert\GreaterThan('-125 years', message: 'Une vraie date de naissance, ce serait mieux ! :)')]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $birthDate;

    #[Assert\Valid]
    #[ORM\Embedded(class: Address::class)]
    private Address $address;

    #[Assert\NotBlank]
    #[AssertPhoneNumber(regionPath: 'address.countryCode')]
    #[ORM\Column(type: 'phone_number')]
    private PhoneNumber $phoneNumber;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $biography = null;

    #[Assert\NotBlank(message: 'Le régime alimentaire est obligatoire.', groups: ['profile_update'])]
    #[ORM\Column(type: 'string', nullable: true, enumType: Diet::class, options: [
        'comment' => 'Diet of the user (omnivore, vegetarien, vegan)',
    ])]
    private ?Diet $diet = null;

    #[ORM\Column(type: 'boolean', options: [
        'default' => false,
    ])]
    private bool $glutenIntolerant = false;

    #[ORM\Column(type: 'boolean', options: [
        'default' => false,
    ])]
    private bool $lactoseIntolerant = false;

    #[Assert\Length(max: 255, groups: ['profile_update'])]
    #[ORM\Column(type: 'string', nullable: true, options: [
        'comment' => 'Free field to provide more information about user diet.',
    ])]
    private ?string $dietDetails = null;

    #[ORM\Column(type: 'boolean', options: [
        'default' => false,
    ])]
    private bool $hasDrivingLicence = false;

    /** @var string[] $roles */
    #[ORM\Column(type: 'text[]')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(type: 'boolean', options: [
        'default' => false,
    ])]
    private bool $visibleOnAlterpotesMap = false;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Registration>
     */
    #[ORM\OneToMany(targetEntity: Registration::class, mappedBy: 'user')]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $registrations;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'payer')]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $payments;

    /**
     * @var Collection<int, Companion>
     */
    #[ORM\OneToMany(targetEntity: Companion::class, mappedBy: 'user')]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $companions;

    /**
     * @var Collection<int, Stage>
     */
    #[ORM\ManyToMany(targetEntity: Stage::class, mappedBy: 'preparers')]
    private Collection $preparedStages;

    /**
     * @var Collection<int, Membership>
     */
    #[ORM\OneToMany(targetEntity: Membership::class, mappedBy: 'user', cascade: ['persist'])]
    #[ORM\OrderBy(['endAt' => 'DESC', 'createdAt' => 'DESC'])]
    private Collection $memberships;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = new \DateTimeImmutable();
        $this->registrations = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->companions = new ArrayCollection();
        $this->preparedStages = new ArrayCollection();
        $this->memberships = new ArrayCollection();
    }

    #[Assert\IsTrue(message: 'Le prénom et le nom de famille ne doivent pas être identiques.')]
    public function hasDifferentFirstnameAndLastname(): bool
    {
        return $this->firstName !== $this->lastName;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        if ('' === $identifier = $this->email) {
            throw new \RuntimeException('Found a user with an empty email!');
        }

        return $identifier;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function addRole(string $role): self
    {
        if (!\in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): self
    {
        if (\in_array($role, $this->roles)) {
            unset($this->roles[array_search($role, $this->roles, true)]);
        }

        return $this;
    }

    public function isAdmin(): bool
    {
        return \in_array('ROLE_ADMIN', $this->roles);
    }

    public function isDeveloper(): bool
    {
        return \in_array('ROLE_DEVELOPER', $this->roles);
    }

    public function isPrepa(): bool
    {
        return \in_array('ROLE_PREPA', $this->roles);
    }

    public function isPrepaForStage(Stage $stage): bool
    {
        return $this->getPreparedStages()->contains($stage);
    }

    public function isAlternativeEditor(): bool
    {
        return \in_array('ROLE_ALTERNATIVE_EDITOR', $this->roles);
    }

    public function isInscriptionsManager(): bool
    {
        return \in_array('ROLE_INSCRIPTIONS', $this->roles);
    }

    public function isStatsViewer(): bool
    {
        return \in_array('ROLE_STATS', $this->roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function hasParticipatedToEvent(Event $event): bool
    {
        foreach ($this->getRegistrations() as $registration) {
            if ($event === $registration->getEvent() && $registration->isConfirmed() && $registration->getStartAt() < new \DateTimeImmutable()) {
                return true;
            }
        }

        return \in_array($this, $event->getPreparers());
    }

    /**
     * @see UserInterface
     */
    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPahekoId(): ?string
    {
        return $this->pahekoId;
    }

    public function setPahekoId(string $pahekoId): self
    {
        $this->pahekoId = $pahekoId;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function verifyEmail(): void
    {
        $this->isVerified = true;
    }

    public function unverifyEmail(): void
    {
        $this->isVerified = false;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = mb_strtolower($email);
        $this->isVerified = false;

        return $this;
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

    public function getPublicName(): string
    {
        if (null !== $this->publicName) {
            return $this->publicName;
        }

        return $this->firstName.' '.mb_substr($this->lastName, 0, 1);
    }

    public function setPublicName(?string $publicName): self
    {
        $this->publicName = $publicName;

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

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(PhoneNumber $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function setDiet(?Diet $diet): self
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

    public function hasDrivingLicence(): bool
    {
        return $this->hasDrivingLicence;
    }

    public function setHasDrivingLicence(bool $hasDrivingLicence): self
    {
        $this->hasDrivingLicence = $hasDrivingLicence;

        return $this;
    }

    public function visibleOnAlterpotesMap(): bool
    {
        return $this->visibleOnAlterpotesMap;
    }

    public function setVisibleOnAlterpotesMap(bool $visibleOnAlterpotesMap): self
    {
        $this->visibleOnAlterpotesMap = $visibleOnAlterpotesMap;

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
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    /**
     * @return Collection<int, Companion>
     */
    public function getCompanions(): Collection
    {
        return $this->companions;
    }

    /**
     * @return Collection<int, Stage>
     */
    public function getPreparedStages(): Collection
    {
        return $this->preparedStages;
    }

    public function isMember(): bool
    {
        foreach ($this->memberships as $membership) {
            if ($membership->isValid()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, Membership>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    /**
     * Returns the latest not cancelled membership.
     */
    public function getLatestMembership(): ?Membership
    {
        foreach ($this->memberships as $membership) {
            if (!$membership->isCanceled()) {
                return $membership;
            }
        }

        return null;
    }

    public function addMembership(Membership $membership): void
    {
        $this->memberships->add($membership);
    }
}
