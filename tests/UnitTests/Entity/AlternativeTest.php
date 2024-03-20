<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Entity;

use App\Entity\Alternative;
use App\Entity\Station;
use PHPUnit\Framework\TestCase;

class AlternativeTest extends TestCase
{
    public function testStations(): void
    {
        $stations = [
            new Station('train', 'Gare de train', 10),
            new Station('bus', 'Gare de bus', 20),
        ];

        $alternative = new Alternative();
        $alternative->setStations($stations);

        $this->assertEquals($stations, $alternative->getStations());
    }
}
