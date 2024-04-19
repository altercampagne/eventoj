<?php

declare(strict_types=1);

namespace App\DataFixtures\Events;

use App\DataFixtures\AbstractFixture;
use App\DataFixtures\Util\FixtureBuilder;
use Doctrine\Persistence\ObjectManager;

class EBToComeClosedBookingsFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $event = FixtureBuilder::createEB(
            name: 'EB à venir (fermé)',
            description: 'Voilà un EB dans le futur et dont les réservations sont fermées ! 🥳',
            openingDateForBookings: new \DateTimeImmutable('+1 month'),
        );

        $manager->persist($event);

        $manager->flush();
    }
}
