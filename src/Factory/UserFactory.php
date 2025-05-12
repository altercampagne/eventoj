<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use libphonenumber\PhoneNumberUtil;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return User::class;
    }

    public function admin(): self
    {
        return $this->afterInstantiate(static function (User $user): void {
            $user->addRole('ROLE_ADMIN');
        });
    }

    /**
     * @param string|string[] $roles
     */
    public function roles(array|string $roles): self
    {
        $roles = (array) $roles;

        return $this->afterInstantiate(static function (User $user) use ($roles): void {
            foreach ($roles as $role) {
                $user->addRole($role);
            }
        });
    }

    public function verifiedEmail(): self
    {
        return $this->afterInstantiate(static function (User $user): void {
            $user->verifyEmail();
        });
    }

    public function children(): self
    {
        return $this->with([
            'birthDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-13 years', 'now')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        // Update email to add a random part & avoid errors when faker returns
        // an email which already exists.
        $email = str_replace('@', '-'.self::faker()->word().'@', self::faker()->email());

        return [
            'address' => AddressFactory::new(),
            'birthDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-80 years', '-18 years')),
            'email' => $email,
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'glutenIntolerant' => self::faker()->boolean(),
            'hasDrivingLicence' => self::faker()->boolean(),
            'lactoseIntolerant' => self::faker()->boolean(),
            'password' => '$2y$04$MOoNnQXwXZsqcL2X073nO.qb/ChqT84weFdkGOpdyGkrc8ByNRn42', // "password"
            'phoneNumber' => PhoneNumberUtil::getInstance()->parse(self::faker()->phoneNumber(), 'FR'),
            'visibleOnAlterpotesMap' => self::faker()->boolean(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(User $user): void {})
        ;
    }
}
