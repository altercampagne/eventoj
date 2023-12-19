<?php

declare(strict_types=1);

namespace App\DataFixtures\Events\AT2023;

use App\DataFixtures\AlternativeFixtures;
use App\Entity\Event;
use App\Entity\Stage;
use App\Entity\StageType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [AlternativeFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $event = Event::AT();
        $event
            ->setName('AlterTour 2023')
            ->setPublishedAt(new \DateTimeImmutable('2023-02-01'))
            ->setOpeningDateForBookings(new \DateTimeImmutable('2023-05-01'))
            ->setDescription(<<<END
                Cet été, l’AlterTour roulera du 10 juillet au 19 août, de Montluçon (03) à Besançon (25).
                Ce sera la 16e édition ! 🥳
                END)
        ;

        foreach ($this->getStages($event) as $stage) {
            $manager->persist($stage);
        }

        $manager->persist($event);

        $manager->flush();
    }

    /** @return iterable<Stage> */
    private function getStages(Event $event): iterable
    {
        yield (new Stage($event))
            ->setName('Before')
            ->setType(StageType::BEFORE)
            ->setDescription('Fignolages avant le départ du tour !')
            ->setDate(new \DateTimeImmutable('2023-07-08'))
        ;
        yield (new Stage($event))
            ->setName('Le champ des possibles')
            ->setDescription('Top départ du tour !')
            ->setDate(new \DateTimeImmutable('2023-07-09'))
        ;
    }
}
