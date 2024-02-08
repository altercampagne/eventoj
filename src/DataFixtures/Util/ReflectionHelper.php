<?php

declare(strict_types=1);

namespace App\DataFixtures\Util;

final class ReflectionHelper
{
    public static function setProperty(object $object, string $property, mixed $value): void
    {
        $reflectionProperty = new \ReflectionProperty($object::class, $property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }
}
