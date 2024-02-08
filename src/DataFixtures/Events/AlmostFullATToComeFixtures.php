<?php

declare(strict_types=1);

namespace App\DataFixtures\Events;

use App\DataFixtures\AbstractFixture;
use App\DataFixtures\AlternativeFixtures;
use App\DataFixtures\Util\FixtureBuilder;
use App\Entity\Registration;
use App\Entity\Stage;
use App\Entity\StageRegistration;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AlmostFullATToComeFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [AlternativeFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $event = FixtureBuilder::createAT(
            name: 'AT presque complet',
            description: 'Voilà un AT dans le futur et dont les réservations sont ouvertes mais presque pleines ! 🥳',
        );
        $manager->persist($event);

        foreach ($this->getStaysConfiguration() as $stay) {
            $user = FixtureBuilder::createUser(children: $stay['children'] ?? false);
            $manager->persist($user);

            $registration = new Registration($user, $event);
            $registration->setPricePerDay($stay['price'] ?? $this->getFaker()->numberBetween(20, 55) * 100);
            $registration->setNeedBike($stay['need_bike'] ?? false);

            $stagesRegistrations = [];
            for ($i = $stay['start']; $i <= $stay['end']; ++$i) {
                /** @var Stage $stage */
                $stage = $event->getStages()->get($i - 1);
                $stagesRegistrations[] = new StageRegistration(stage: $stage, registration: $registration);
            }

            $registration->setStagesRegistrations($stagesRegistrations);
            $registration->confirm();

            $manager->persist($registration);
        }

        $manager->flush();
    }

    /**
     * @return iterable<array{start: int, end: int, price?: int, need_bike?: bool, children?: bool}>
     */
    private function getStaysConfiguration(): iterable
    {
        yield ['start' => 1, 'end' => 31, 'need_bike' => true];
        yield ['start' => 1, 'end' => 31, 'need_bike' => true];
        yield ['start' => 1, 'end' => 31, 'need_bike' => true];
        yield ['start' => 1, 'end' => 31, 'children' => true];
        yield ['start' => 1, 'end' => 31, 'children' => true];
        yield ['start' => 1, 'end' => 31, 'children' => true];
        yield ['start' => 1, 'end' => 31];
        yield ['start' => 5, 'end' => 31];
        yield ['start' => 5, 'end' => 31];
        yield ['start' => 7, 'end' => 25];
        yield ['start' => 7, 'end' => 25];
        yield ['start' => 9, 'end' => 14, 'need_bike' => true];
        yield ['start' => 13, 'end' => 20];
        yield ['start' => 13, 'end' => 20, 'children' => true];
        yield ['start' => 19, 'end' => 25, 'children' => true];
    }
}
