<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;

trait DatabaseUtilTrait
{
    protected function getRandomUser(): User
    {
        /** @var UserRepository $repository */
        $repository = static::getContainer()->get(UserRepository::class);

        $users = $repository->findAll();

        return $users[array_rand($users)];
    }
}
