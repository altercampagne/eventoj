<?php

declare(strict_types=1);

namespace App\DataFixtures\Events;

use App\DataFixtures\AbstractFixture;
use App\DataFixtures\Util\FixtureBuilder;
use App\Entity\Registration;
use App\Entity\Stage;
use App\Entity\StageRegistration;
use Doctrine\Persistence\ObjectManager;

class EBToComeFullFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $event = FixtureBuilder::createEB(
            name: 'EB à venir (ouvert mais complet)',
            description: 'Voilà un EB dans le futur et dont les réservations sont ouvertes même s\'il n\'y a plus de places ! 🥳',
            openingDateForBookings: new \DateTimeImmutable('-1 month'),
            adultsCapacity: 10,
            childrenCapacity: 0,
            bikesAvailable: 0,
        );

        for ($i = 0; $i < 10; ++$i) {
            $user = FixtureBuilder::createUser(children: false);
            $manager->persist($user);

            $registration = new Registration($user, $event);
            $registration->setNeededBike(0);

            $stagesRegistrations = [];
            foreach ($event->getStages() as $stage) {
                /* @var Stage $stage */
                $stagesRegistrations[] = new StageRegistration(stage: $stage, registration: $registration);
            }

            $registration->setStagesRegistrations($stagesRegistrations);
            $registration->setPrice($this->getFaker()->numberBetween(20, 55) * \count($stagesRegistrations));
            $registration->confirm();

            $manager->persist($registration);
        }

        $manager->persist($event);

        $manager->flush();
    }
}
