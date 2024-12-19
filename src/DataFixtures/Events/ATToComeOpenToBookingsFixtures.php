<?php

declare(strict_types=1);

namespace App\DataFixtures\Events;

use App\DataFixtures\AbstractFixture;
use App\DataFixtures\AlternativeFixtures;
use App\DataFixtures\Events\AT2023\EventFixtures as AT2023EventFixtures;
use App\Factory\EventFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ATToComeOpenToBookingsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly AT2023EventFixtures $at2023EventFixtures,
    ) {
    }

    public function getDependencies(): array
    {
        return [AlternativeFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $event = EventFactory::new()->AT()->published()->create([
            'name' => 'AT à venir (ouvert)',
            'description' => 'Voilà un AT dans le futur et dont les réservations sont ouvertes ! 🥳',
        ])->_real();

        $startDate = $this->getStartDate();
        foreach ($this->at2023EventFixtures->getStages($event) as $stage) {
            $stage->setDate(\DateTimeImmutable::createFromMutable($startDate));

            $manager->persist($stage);

            $startDate->modify('+1 day');
        }

        $manager->persist($event);

        $manager->flush();
    }

    private function getStartDate(): \DateTime
    {
        $startDate = new \DateTime(date('Y').'-07-10');

        if ($startDate > new \DateTime()) {
            return $startDate;
        }

        return $startDate->modify('+1 year');
    }
}
