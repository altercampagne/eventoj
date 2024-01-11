<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private readonly PhoneNumberUtil $phoneNumberUtil;
    private readonly Generator $faker;
    private ?string $hashedPassword = null;

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        PhoneNumberUtil $phoneNumberUtil = null,
    ) {
        $this->phoneNumberUtil = $phoneNumberUtil ?: PhoneNumberUtil::getInstance();
        $this->faker = \Faker\Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user
            ->setEmail('admin@altercampagne.net')
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setBirthDate(\DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-80 years', 'now')))
            ->setAddress($this->getRandomAddress())
            ->setPhoneNumber($this->phoneNumberUtil->parse($this->faker->phoneNumber(), 'FR'))
            ->setRoles(['ROLE_ADMIN'])
        ;
        $user->setPassword($this->getHashedPassword());
        $user->verifyEmail();

        $manager->persist($user);

        $user = $this->createRandomUser('change-my-password@test-only.user');
        $manager->persist($user);

        for ($i = 0; $i < 50; ++$i) {
            $user = $this->createRandomUser();
            $manager->persist($user);

            $this->setReference("user-$i", $user);
        }

        $manager->flush();
    }

    private function createRandomUser(string $email = null): User
    {
        $user = new User();
        $user
            ->setEmail($email ?: $this->faker->email())
            ->setFirstName($this->faker->firstName())
            ->setLastName($this->faker->lastName())
            ->setBirthDate(\DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-80 years', 'now')))
            ->setAddress($this->getRandomAddress())
            ->setPhoneNumber($this->phoneNumberUtil->parse($this->faker->phoneNumber(), 'FR'))
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->getHashedPassword())
        ;

        return $user;
    }

    private function getRandomAddress(): Address
    {
        $address = new Address();
        $address
            ->setAddressLine1($this->faker->streetAddress())
            ->setAddressLine2($this->faker->optional(20)->streetAddress())
            ->setCity($this->faker->city())
            ->setZipCode($this->faker->postCode())
            ->setCountryCode($this->faker->countryCode())
        ;

        return $address;
    }

    private function getHashedPassword(): string
    {
        if (null === $this->hashedPassword) {
            $this->hashedPassword = $this->userPasswordHasher->hashPassword(new User(), 'password');
        }

        return $this->hashedPassword;
    }
}
