<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\EventFactory;
use App\Factory\RegistrationFactory;
use App\Factory\UserFactory;
use App\Story\AlterTour2023Story;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // The famous AlterTour2023
        AlterTour2023Story::load();

        // A random AT which is almost full
        $event = EventFactory::new()->AT()->published()->withRandomStages('first day of July', 31)->create([
            'name' => 'AT presque complet',
            'description' => 'Voilà un AT dans le futur et dont les réservations sont ouvertes mais presque pleines ! 🥳',
        ])->_real();

        foreach ($this->getStaysConfiguration() as $stay) {
            $user = $stay['children'] ?? false ? UserFactory::new()->children()->create()->_real() : UserFactory::createOne()->_real();

            RegistrationFactory::new()->confirmed()->withStagesRegistrations($stay['start'] - 1, $stay['end'])->create([
                'user' => $user,
                'event' => $event,
                'neededBike' => $stay['needed_bike'] ?? 0,
            ]);
        }

        // A coming AT without any registration
        EventFactory::new()->AT()->published()->withRandomStages()->create([
            'name' => 'AT à venir (ouvert)',
            'description' => 'Voilà un AT dans le futur et dont les réservations sont ouvertes ! 🥳',
        ]);

        // A coming EB without any registration
        EventFactory::new()->EB()->published()->withRandomStages('first day of August', 10)->create([
            'name' => 'EB à venir (ouvert)',
            'description' => 'Voilà un EB dans le futur et dont les réservations sont ouvertes ! 🥳',
        ]);

        // A coming EB without any registration but not (yet) open for bookings
        EventFactory::new()->EB()->published()->withRandomStages('first day of August', 10)->create([
            'name' => 'EB à venir (fermé)',
            'description' => 'Voilà un EB dans le futur et dont les réservations sont fermées ! 🥳',
            'openingDateForBookings' => new \DateTimeImmutable('+1 month'),
        ]);

        // A coming EB which is full
        RegistrationFactory::new()->withStagesRegistrations()->confirmed()->many(10)->create([
            'event' => EventFactory::new()->EB()->published()->withRandomStages('first day of August', 10)->create([
                'name' => 'EB à venir (ouvert mais complet)',
                'description' => 'Voilà un EB dans le futur et dont les réservations sont ouvertes même s\'il n\'y a plus de places ! 🥳',
                'openingDateForBookings' => new \DateTimeImmutable('-1 month'),
                'adultsCapacity' => 10,
                'childrenCapacity' => 0,
                'bikesAvailable' => 0,
            ]),
        ]);
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }

    /**
     * @return iterable<array{start: int, end: int, price?: int, needed_bike?: int, children?: bool}>
     */
    private function getStaysConfiguration(): iterable
    {
        yield ['start' => 1, 'end' => 31, 'needed_bike' => 1];
        yield ['start' => 1, 'end' => 31, 'needed_bike' => 1];
        yield ['start' => 1, 'end' => 31, 'needed_bike' => 1];
        yield ['start' => 1, 'end' => 31, 'children' => true];
        yield ['start' => 1, 'end' => 31, 'children' => true];
        yield ['start' => 1, 'end' => 31, 'children' => true];
        yield ['start' => 1, 'end' => 31];
        yield ['start' => 5, 'end' => 31];
        yield ['start' => 5, 'end' => 31];
        yield ['start' => 7, 'end' => 25];
        yield ['start' => 7, 'end' => 25];
        yield ['start' => 9, 'end' => 14, 'needed_bike' => 1];
        yield ['start' => 13, 'end' => 20];
        yield ['start' => 13, 'end' => 20, 'children' => true];
        yield ['start' => 19, 'end' => 25, 'children' => true];
    }
}
