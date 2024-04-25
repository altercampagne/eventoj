<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Entity;

use App\Entity\Meal;
use PHPUnit\Framework\TestCase;

class MealTest extends TestCase
{
    public function testIsBeforeAndAfter(): void
    {
        $breakfast = Meal::BREAKFAST;
        $lunch = Meal::LUNCH;
        $dinner = Meal::DINNER;

        $this->assertFalse($breakfast->isBefore($breakfast));
        $this->assertTrue($breakfast->isBefore($lunch));
        $this->assertTrue($breakfast->isBefore($dinner));

        $this->assertFalse($breakfast->isAfter($breakfast));
        $this->assertFalse($breakfast->isAfter($lunch));
        $this->assertFalse($breakfast->isAfter($dinner));

        $this->assertFalse($lunch->isBefore($breakfast));
        $this->assertFalse($lunch->isBefore($lunch));
        $this->assertTrue($lunch->isBefore($dinner));

        $this->assertTrue($lunch->isAfter($breakfast));
        $this->assertFalse($lunch->isAfter($lunch));
        $this->assertFalse($lunch->isAfter($dinner));

        $this->assertFalse($dinner->isBefore($breakfast));
        $this->assertFalse($dinner->isBefore($lunch));
        $this->assertFalse($dinner->isBefore($dinner));

        $this->assertTrue($dinner->isAfter($breakfast));
        $this->assertTrue($dinner->isAfter($lunch));
        $this->assertFalse($dinner->isAfter($dinner));
    }
}
