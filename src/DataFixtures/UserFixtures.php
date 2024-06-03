<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Util\FixtureBuilder;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $admin = FixtureBuilder::createUser(
            email: 'admin@altercampagne.net',
            firstName: 'John',
            lastName: 'Doe',
            roles: ['ROLE_ADMIN', 'ROLE_DEVELOPER'],
            verifyEmail: true,
        );

        $manager->persist($admin);
        $manager->persist(FixtureBuilder::createCompanion(
            user: $admin,
            firstName: 'Bambino',
            lastName: 'Petit',
            birthDate: new \DateTimeImmutable('-1 year'),
        ));
        $manager->persist(FixtureBuilder::createCompanion(
            user: $admin,
            firstName: 'Bambino',
            lastName: 'Moyen',
            birthDate: new \DateTimeImmutable('-8 years'),
        ));
        // Following child whild become "adult" (13+ years old) just before the beginning of the event.
        // Useful to check which date is used during registration!
        $birthDate = new \DateTimeImmutable('-13 years');
        $birthDate = $birthDate->setDate((int) $birthDate->format('Y'), 6, 25);
        $manager->persist(FixtureBuilder::createCompanion(
            user: $admin,
            firstName: 'Bambino',
            lastName: 'QuiVaÊtreAdulte',
            birthDate: $birthDate,
        ));
        $manager->persist(FixtureBuilder::createCompanion(
            user: $admin,
            firstName: 'Bambino',
            lastName: 'Grand',
            birthDate: new \DateTimeImmutable('-15 years'),
        ));
        $manager->persist(FixtureBuilder::createCompanion(
            user: $admin,
            firstName: 'Copain',
            lastName: 'Adulte',
            birthDate: new \DateTimeImmutable('-35 years'),
        ));

        // Create a membership for previous year. In order to be sure its a
        // past membership, we first create a active one in order to use it's
        // start date.
        $membership = FixtureBuilder::createMembershipForUser(user: $admin);
        $membership = FixtureBuilder::createMembershipForUser(user: $admin, startAt: $membership->getStartAt()->modify('-1 year'));

        $manager->persist($membership->getPayment());
        $manager->persist($membership);

        $manager->persist(FixtureBuilder::createUser(email: 'change-my-password@test-only.user'));

        $manager->flush();
    }
}
