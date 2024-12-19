<?php

declare(strict_types=1);

namespace App\DataFixtures\Events;

use App\DataFixtures\AbstractFixture;
use App\Entity\Registration;
use App\Entity\Stage;
use App\Entity\StageRegistration;
use App\Factory\EventFactory;
use App\Factory\UserFactory;
use Doctrine\Persistence\ObjectManager;

class EBToComeFullFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $event = EventFactory::new()->EB()->published()->withStages('first day of August', 10)->create([
            'name' => 'EB à venir (ouvert mais complet)',
            'description' => 'Voilà un EB dans le futur et dont les réservations sont ouvertes même s\'il n\'y a plus de places ! 🥳',
            'openingDateForBookings' => new \DateTimeImmutable('-1 month'),
            'adultsCapacity' => 10,
            'childrenCapacity' => 0,
            'bikesAvailable' => 0,
        ])->_real();

        for ($i = 0; $i < 10; ++$i) {
            $user = UserFactory::createOne()->_real();

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

        $manager->flush();
    }
}
