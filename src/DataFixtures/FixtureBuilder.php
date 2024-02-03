<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\User;
use Faker\Generator;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;

class FixtureBuilder
{
    private static ?Generator $faker = null;

    /**
     * @param string[] $roles
     */
    public static function createUser(
        string $email = null,
        string $firstName = null,
        string $lastName = null,
        \DateTimeImmutable $birthDate = null,
        Address $address = null,
        PhoneNumber $phoneNumber = null,
        string $password = null,
        bool $verifyEmail = null,
        array $roles = [],
        bool $children = false,
    ): User {
        $user = new User();
        $user
            ->setEmail($email ?? self::getFaker()->email())
            ->setFirstName($firstName ?? self::getFaker()->firstName())
            ->setLastName($lastName ?? self::getFaker()->lastName())
            ->setBirthDate($birthDate ?? \DateTimeImmutable::createFromMutable($children ? self::getFaker()->dateTimeBetween('-12 years', 'now') : self::getFaker()->dateTimeBetween('-80 years', '-14 years')))
            ->setAddress($address ?? self::createAddress())
            ->setPhoneNumber($phoneNumber ?? PhoneNumberUtil::getInstance()->parse(self::getFaker()->phoneNumber(), 'FR'))
            ->setRoles($roles)
            ->setPassword($password ?? '$2y$04$MOoNnQXwXZsqcL2X073nO.qb/ChqT84weFdkGOpdyGkrc8ByNRn42') // "password"
        ;

        if ($verifyEmail ?? self::getFaker()->boolean()) {
            $user->verifyEmail();
        }

        return $user;
    }

    public static function createAddress(
        string $addressLine1 = null,
        string $addressLine2 = null,
        string $city = null,
        string $zipCode = null,
        string $countryCode = 'FR',
    ): Address {
        $address = new Address();
        $address
            ->setAddressLine1($addressLine1 ?? self::getFaker()->streetAddress())
            ->setAddressLine2($addressLine2 ?? self::getFaker()->optional(20)->streetAddress())
            ->setCity($city ?? self::getFaker()->city())
            ->setZipCode($zipCode ?? self::getFaker()->postCode())
            ->setCountryCode($countryCode)
        ;

        return $address;
    }

    private static function getFaker(): Generator
    {
        if (null === self::$faker) {
            self::$faker = \Faker\Factory::create('fr_FR');
        }

        return self::$faker;
    }
}
