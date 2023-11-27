<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private readonly PhoneNumberUtil $phoneNumberUtil;

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        PhoneNumberUtil $phoneNumberUtil = null,
    ) {
        $this->phoneNumberUtil = $phoneNumberUtil ?: PhoneNumberUtil::getInstance();
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $user = new User(
            email: 'admin@altercampagne.net',
            firstName: 'Super',
            lastName: 'Admin',
            birthDate: \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-80 years', 'now')),
            address: new Address(
                addressLine1: $faker->streetAddress(),
                addressLine2: $faker->optional(20)->streetAddress(),
                city: $faker->city(),
                zipCode: $faker->postCode(),
                countryCode: $faker->countryCode(),
            ),
            phoneNumber: $this->phoneNumberUtil->parse($faker->phoneNumber(), 'FR'),
        );
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        for ($i = 0; $i < 50; ++$i) {
            $user = new User(
                email: $faker->email(),
                firstName: $faker->firstName(),
                lastName: $faker->lastName(),
                birthDate: \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-80 years', 'now')),
                address: new Address(
                    addressLine1: $faker->streetAddress(),
                    addressLine2: $faker->optional(20)->streetAddress(),
                    city: $faker->city(),
                    zipCode: $faker->postCode(),
                    countryCode: $faker->countryCode(),
                ),
                phoneNumber: $this->phoneNumberUtil->parse($faker->phoneNumber(), 'FR'),
            );
            $user->setPassword($hashedPassword);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
