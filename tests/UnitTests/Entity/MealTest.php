<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Entity;

use App\Entity\Meal;
use PHPUnit\Framework\TestCase;

class MealTest extends TestCase
{
    public function testMeal(): void
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

        $this->assertSame([$lunch, $dinner], $breakfast->getFollowingMeals());
        $this->assertSame([$breakfast, $lunch, $dinner], $breakfast->getFollowingMeals(includeSelf: true));
        $this->assertSame([], $breakfast->getPreviousMeals());
        $this->assertSame([$breakfast], $breakfast->getPreviousMeals(includeSelf: true));

        $this->assertSame([$dinner], $lunch->getFollowingMeals());
        $this->assertSame([$lunch, $dinner], $lunch->getFollowingMeals(includeSelf: true));
        $this->assertSame([$breakfast], $lunch->getPreviousMeals());
        $this->assertSame([$breakfast, $lunch], $lunch->getPreviousMeals(includeSelf: true));

        $this->assertSame([], $dinner->getFollowingMeals());
        $this->assertSame([$dinner], $dinner->getFollowingMeals(includeSelf: true));
        $this->assertSame([$breakfast, $lunch], $dinner->getPreviousMeals());
        $this->assertSame([$breakfast, $lunch, $dinner], $dinner->getPreviousMeals(includeSelf: true));
    }
}
