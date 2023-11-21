<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $event = new Event('AlterTour 2023', <<<EOL
            Cet été, l’AlterTour roulera du 10 juillet au 19 août, de Montluçon (03) à Besançon (25).
            Ce sera la 16e édition de l'AlterTour ! 🥳
            EOL);
        $manager->persist($event);

        $event = new Event('AlterTour 2022', <<<EOL
            C'était l'AlterTour d'encore avant !
            Ce sera la 15e édition de l'AlterTour ! 🥳
            EOL);
        $manager->persist($event);

        $manager->flush();
    }
}
