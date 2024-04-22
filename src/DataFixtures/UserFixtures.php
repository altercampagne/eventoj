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

        $manager->persist(FixtureBuilder::createUser(email: 'change-my-password@test-only.user'));

        $manager->flush();
    }
}
