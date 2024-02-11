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
            roles: ['ROLE_ADMIN'],
            verifyEmail: true,
        );

        $manager->persist($admin);
        $manager->persist(FixtureBuilder::createCompanion(user: $admin, children: false));
        $manager->persist(FixtureBuilder::createCompanion(user: $admin, children: true));

        $manager->persist(FixtureBuilder::createUser(email: 'change-my-password@test-only.user'));

        $manager->flush();
    }
}
