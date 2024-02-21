<?php

declare(strict_types=1);

namespace App\DataFixtures\Util;

use App\Entity\Address;
use App\Entity\Companion;
use App\Entity\Diet;
use App\Entity\Event;
use App\Entity\Stage;
use App\Entity\UploadedFile;
use App\Entity\UploadedFileType;
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
        ?string $email = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?\DateTimeImmutable $birthDate = null,
        ?Address $address = null,
        ?PhoneNumber $phoneNumber = null,
        ?string $password = null,
        ?bool $verifyEmail = null,
        ?array $roles = null,
        bool $children = false,
        bool $admin = false,
    ): User {
        $user = new User();
        $user
            ->setEmail($email ?? self::getFaker()->email())
            ->setFirstName($firstName ?? self::getFaker()->firstName())
            ->setLastName($lastName ?? self::getFaker()->lastName())
            ->setBirthDate($birthDate ?? \DateTimeImmutable::createFromMutable($children ? self::getFaker()->dateTimeBetween('-12 years', 'now') : self::getFaker()->dateTimeBetween('-80 years', '-14 years')))
            ->setAddress($address ?? self::createAddress())
            ->setPhoneNumber($phoneNumber ?? PhoneNumberUtil::getInstance()->parse(self::getFaker()->phoneNumber(), 'FR'))
            ->setRoles($roles ?? $admin ? ['ROLE_ADMIN'] : [])
            ->setPassword($password ?? '$2y$04$MOoNnQXwXZsqcL2X073nO.qb/ChqT84weFdkGOpdyGkrc8ByNRn42') // "password"
        ;

        if ($verifyEmail ?? self::getFaker()->boolean()) {
            $user->verifyEmail();
        }

        return $user;
    }

    public static function createAddress(
        ?string $addressLine1 = null,
        ?string $addressLine2 = null,
        ?string $city = null,
        ?string $zipCode = null,
        string $countryCode = 'FR',
    ): Address {
        $address = new Address();
        $address
            ->setAddressLine1($addressLine1 ?? self::getFaker()->streetAddress())
            ->setAddressLine2($addressLine2 ?? self::getFaker()->optional(0.2)->streetAddress())
            ->setCity($city ?? self::getFaker()->city())
            ->setZipCode($zipCode ?? self::getFaker()->postCode())
            ->setCountryCode($countryCode)
        ;

        return $address;
    }

    public static function createAT(
        ?string $name = null,
        ?string $description = null,
        int $adultsCapacity = 10,
        int $childrenCapacity = 5,
        int $bikesAvailable = 5,
        bool $published = true,
    ): Event {
        $event = Event::AT();
        $event
            ->setName($name ?? self::getFaker()->word())
            ->setOpeningDateForBookings(new \DateTimeImmutable())
            ->setDescription($description ?? self::getFaker()->sentence())
        ;
        ReflectionHelper::setProperty($event, 'adultsCapacity', $adultsCapacity);
        ReflectionHelper::setProperty($event, 'childrenCapacity', $childrenCapacity);
        ReflectionHelper::setProperty($event, 'bikesAvailable', $bikesAvailable);

        if ($published) {
            $event->setPublishedAt(new \DateTimeImmutable());
        }

        self::createStagesForEvent($event, 'first day of July', 31);

        return $event;
    }

    public static function createEB(
        ?string $name = null,
        ?string $description = null,
        int $adultsCapacity = 10,
        int $childrenCapacity = 5,
        int $bikesAvailable = 5,
        bool $published = true,
    ): Event {
        $event = Event::EB();
        $event
            ->setName($name ?? self::getFaker()->word())
            ->setOpeningDateForBookings(new \DateTimeImmutable())
            ->setDescription($description ?? self::getFaker()->sentence())
        ;
        ReflectionHelper::setProperty($event, 'adultsCapacity', $adultsCapacity);
        ReflectionHelper::setProperty($event, 'childrenCapacity', $childrenCapacity);
        ReflectionHelper::setProperty($event, 'bikesAvailable', $bikesAvailable);

        if ($published) {
            $event->setPublishedAt(new \DateTimeImmutable());
        }

        self::createStagesForEvent($event, 'first day of August', 10);

        return $event;
    }

    public static function createStagesForEvent(Event $event, string $dateTime, int $numberOfStages): void
    {
        $date = new \DateTimeImmutable($dateTime);
        if ($date < new \DateTimeImmutable()) {
            $date = $date->modify('+1 year');
        }

        for ($i = 1; $i <= $numberOfStages; ++$i) {
            $stage = (new Stage($event))
                ->setName("Day #$i")
                ->setDescription("Jour #$i")
            ;
            $stage->setDate($date);

            $date = $date->modify('+1 day');

            $event->addStage($stage);
        }
    }

    public static function createCompanion(
        ?User $user = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?\DateTimeImmutable $birthDate = null,
        ?string $email = null,
        ?PhoneNumber $phoneNumber = null,
        Diet $diet = Diet::VEGETARIAN,
        bool $glutenIntolerant = false,
        bool $lactoseIntolerant = false,
        ?string $dietDetails = null,
        bool $children = false,
    ): Companion {
        $companion = new Companion($user ?: self::createUser());
        $companion
            ->setFirstName($firstName ?? self::getFaker()->firstName())
            ->setLastName($lastName ?? self::getFaker()->lastName())
            ->setBirthDate($birthDate ?? \DateTimeImmutable::createFromMutable($children ? self::getFaker()->dateTimeBetween('-12 years', 'now') : self::getFaker()->dateTimeBetween('-80 years', '-14 years')))
            ->setEmail($email ?? self::getFaker()->optional()->email())
            ->setPhoneNumber($phoneNumber ?? PhoneNumberUtil::getInstance()->parse(self::getFaker()->phoneNumber(), 'FR'))
            ->setDiet($diet)
            ->setGlutenIntolerant($glutenIntolerant)
            ->setLactoseIntolerant($lactoseIntolerant)
            ->setDietDetails($dietDetails)
        ;

        return $companion;
    }

    public static function createUploadedFile(
        UploadedFileType $type = UploadedFileType::EVENT,
        string $path = 'event/altertour-2023.jpg',
        ?string $originalFileName = null,
        ?int $size = null,
        ?string $mimeType = null,
    ): UploadedFile {
        $file = new UploadedFile($type, $path, $originalFileName ?? (self::getFaker()->word().'.'.self::getFaker()->fileExtension()));
        $file->setSize($size ?? self::getFaker()->optional()->randomDigit());
        $file->setMimeType($mimeType ?? self::getFaker()->optional()->mimeType());

        return $file;
    }

    private static function getFaker(): Generator
    {
        if (null === self::$faker) {
            self::$faker = \Faker\Factory::create('fr_FR');
        }

        return self::$faker;
    }
}
