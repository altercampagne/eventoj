<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User('admin@altercampagne.ovh', 'Super admin');
        $user->setPassword('password');
        $manager->persist($user);

        $manager->flush();
    }
}
