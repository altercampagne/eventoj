<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\CompanionFactory;
use App\Factory\MembershipFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $admin = UserFactory::new()->roles(['ROLE_ADMIN', 'ROLE_DEVELOPER'])->verifiedEmail()->create([
            'email' => 'admin@altercampagne.net',
            'firstName' => 'John',
            'lastName' => 'Doe',
        ])->_real();

        CompanionFactory::createOne([
            'user' => $admin,
            'firstName' => 'Bambino',
            'lastName' => 'Petit',
            'birthDate' => new \DateTimeImmutable('-1 year'),
        ]);
        CompanionFactory::createOne([
            'user' => $admin,
            'firstName' => 'Bambino',
            'lastName' => 'Moyen',
            'birthDate' => new \DateTimeImmutable('-8 years'),
        ]);

        // Following child whild become "adult" (13+ years old) just before the beginning of the event.
        // Useful to check which date is used during registration!
        $birthDate = new \DateTimeImmutable('-13 years');
        $birthDate = $birthDate->setDate((int) $birthDate->format('Y'), 6, 25);
        CompanionFactory::createOne([
            'user' => $admin,
            'firstName' => 'Bambino',
            'lastName' => 'QuiVaÊtreAdulte',
            'birthDate' => $birthDate,
        ]);
        CompanionFactory::createOne([
            'user' => $admin,
            'firstName' => 'Bambino',
            'lastName' => 'Grand',
            'birthDate' => new \DateTimeImmutable('-15 years'),
        ]);
        CompanionFactory::createOne([
            'user' => $admin,
            'firstName' => 'Copain',
            'lastName' => 'Adulte',
            'birthDate' => new \DateTimeImmutable('-35 years'),
        ]);

        // Create a membership for previous year. In order to be sure its a
        // past membership, we first create a active one in order to use it's
        // start date.
        $membership = MembershipFactory::createOne(['user' => $admin]);
        MembershipFactory::createOne(['user' => $admin, 'startAt' => $membership->getStartAt()->modify('-1 year')]);

        UserFactory::createOne([
            'email' => 'change-my-password@test-only.user',
        ])->_real();
    }
}
