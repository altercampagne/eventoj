<?php

declare(strict_types=1);

namespace App\DataFixtures\Events;

use App\DataFixtures\AbstractFixture;
use App\Factory\EventFactory;
use Doctrine\Persistence\ObjectManager;

class EBToComeOpenToBookingsFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        EventFactory::new()->EB()->published()->withStages('first day of August', 10)->create([
            'name' => 'EB à venir (ouvert)',
            'description' => 'Voilà un EB dans le futur et dont les réservations sont ouvertes ! 🥳',
        ]);
    }
}
