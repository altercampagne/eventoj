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
            ->setFirstName('Super')
            ->setLastName('Admin')
            ->setBirthDate(\DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-80 years', 'now')))
            ->setAddress($this->getRandomAddress())
            ->setPhoneNumber($this->phoneNumberUtil->parse($this->faker->phoneNumber(), 'FR'))
            ->setRoles(['ROLE_ADMIN'])
        ;
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);
        $user->verifyEmail();

        $manager->persist($user);

        for ($i = 0; $i < 50; ++$i) {
            $user = new User();
            $user
                ->setEmail($this->faker->email())
                ->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->lastName())
                ->setBirthDate(\DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-80 years', 'now')))
                ->setAddress($this->getRandomAddress())
                ->setPhoneNumber($this->phoneNumberUtil->parse($this->faker->phoneNumber(), 'FR'))
                ->setRoles(['ROLE_ADMIN'])
                ->setPassword($hashedPassword)
            ;
            $manager->persist($user);
        }

        $manager->flush();
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
}
