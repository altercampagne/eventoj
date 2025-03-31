<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Entity\Document;

use App\Entity\Document\Dimensions;
use PHPUnit\Framework\TestCase;

final class DimensionsTest extends TestCase
{
    public function testSizeWithNullValues(): void
    {
        $dimensions = new Dimensions();

        self::assertTrue($dimensions->isLandscape());
        self::assertFalse($dimensions->isPortrait());

        self::assertSame(1568, $dimensions->getWidthLarge());
        self::assertSame(1568, $dimensions->getWidth('lg'));
        self::assertSame(1568, $dimensions->getWidth('large'));
        self::assertSame(640, $dimensions->getWidthMedium());
        self::assertSame(640, $dimensions->getWidth('md'));
        self::assertSame(640, $dimensions->getWidth('medium'));
        self::assertSame(320, $dimensions->getWidthSmall());
        self::assertSame(320, $dimensions->getWidth('sm'));
        self::assertSame(320, $dimensions->getWidth('small'));

        self::assertSame(1176, $dimensions->getHeightLarge());
        self::assertSame(1176, $dimensions->getHeight('lg'));
        self::assertSame(1176, $dimensions->getHeight('large'));
        self::assertSame(480, $dimensions->getHeightMedium());
        self::assertSame(480, $dimensions->getHeight('md'));
        self::assertSame(480, $dimensions->getHeight('medium'));
        self::assertSame(240, $dimensions->getHeightSmall());
        self::assertSame(240, $dimensions->getHeight('sm'));
        self::assertSame(240, $dimensions->getHeight('small'));
    }

    public function testSizeWithLandscapeImage(): void
    {
        $dimensions = new Dimensions(4032, 3024);

        self::assertTrue($dimensions->isLandscape());
        self::assertFalse($dimensions->isPortrait());

        self::assertSame(1568, $dimensions->getWidthLarge());
        self::assertSame(640, $dimensions->getWidthMedium());
        self::assertSame(320, $dimensions->getWidthSmall());

        self::assertSame(1176, $dimensions->getHeightLarge());
        self::assertSame(480, $dimensions->getHeightMedium());
        self::assertSame(240, $dimensions->getHeightSmall());
    }

    public function testSizeWithPortraitImage(): void
    {
        $dimensions = new Dimensions(3024, 4032);

        self::assertFalse($dimensions->isLandscape());
        self::assertTrue($dimensions->isPortrait());

        self::assertSame(1176, $dimensions->getWidthLarge());
        self::assertSame(480, $dimensions->getWidthMedium());
        self::assertSame(240, $dimensions->getWidthSmall());

        self::assertSame(1568, $dimensions->getHeightLarge());
        self::assertSame(640, $dimensions->getHeightMedium());
        self::assertSame(320, $dimensions->getHeightSmall());
    }
}
