<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User('admin@altercampagne.net', 'Super admin');
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, 'password');
        $user
            ->setPassword($hashedPassword)
            ->setRoles(['ROLE_ADMIN'])
        ;
        $manager->persist($user);

        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 50; ++$i) {
            $user = new User($faker->email(), $faker->name());
            $user->setPassword($hashedPassword);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
