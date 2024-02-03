<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;

class UserFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(FixtureBuilder::createUser(
            email: 'admin@altercampagne.net',
            firstName: 'John',
            lastName: 'Doe',
            roles: ['ROLE_ADMIN'],
            verifyEmail: true,
        ));

        $manager->persist(FixtureBuilder::createUser(email: 'change-my-password@test-only.user'));

        $manager->flush();
    }
}
