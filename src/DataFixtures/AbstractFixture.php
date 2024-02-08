<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Generator;

abstract class AbstractFixture extends Fixture
{
    private ?Generator $faker = null;

    protected function getFaker(): Generator
    {
        if (null === $this->faker) {
            $this->faker = \Faker\Factory::create('fr_FR');
        }

        return $this->faker;
    }
}
