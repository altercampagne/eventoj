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

    /**
     * Use Reflection to set a protected / private property.
     */
    protected function setProperty(object $object, string $property, mixed $value): void
    {
        $reflectionProperty = new \ReflectionProperty($object::class, $property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }
}
