<?php

declare(strict_types=1);

namespace App\DataFixtures\Events\AT2023;

use App\DataFixtures\AlternativeFixtures;
use App\DataFixtures\FixturesHelperTrait;
use App\Entity\Alternative;
use App\Entity\Event;
use App\Entity\Registration;
use App\Entity\Stage;
use App\Entity\StageAlternativeRelation;
use App\Entity\StageType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    use FixturesHelperTrait;

    private readonly Generator $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create('fr_FR');
    }

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

        $startDate = new \DateTime('2023-07-08');
        foreach ($this->getStages($event) as $stage) {
            $stage->setDate(\DateTimeImmutable::createFromMutable($startDate));

            $manager->persist($stage);

            $startDate->modify('+1 day');
        }

        $manager->persist($event);

        for ($i = 0; $i < 100; ++$i) {
            $registration = $this->generateRegistration($event);
            $manager->persist($registration);
        }

        $manager->flush();
    }

    /**
     * @return iterable<Stage>
     */
    public function getStages(Event $event): iterable
    {
        yield (new Stage($event))
            ->setName('Before')
            ->setType(StageType::BEFORE)
            ->setDescription('Fignolages avant le départ du tour !')
            ->addAlternative($this->getAlternative('le-champ-des-possibles'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Le champ des possibles')
            ->setDescription('Top départ du tour !')
            ->addAlternative($this->getAlternative('le-champ-des-possibles'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('De Montluçon à Hérisson')
            ->setDescription('De Montluçon à Hérisson pour rendre visite à Josselin & Lisa.')
            ->addAlternative($this->getAlternative('le-champ-des-possibles'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('les-champs-de-lile'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Journée chantiers à Hérisson')
            ->setDescription('On file un coup de main à Josselin & Lisa !')
            ->addAlternative($this->getAlternative('les-champs-de-lile'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('De Hérisson à Tortezais')
            ->setDescription('On va rendre visite à Jean-Louis Gaby')
            ->addAlternative($this->getAlternative('les-champs-de-lile'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('solaire-2000'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('De Tortezais au manois de la Beaume')
            ->setDescription('Hop, ça continue !')
            ->addAlternative($this->getAlternative('solaire-2000'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('le-manoir-de-la-beaume'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('On reste au manoir')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('le-manoir-de-la-beaume'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('On reste au manoir')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('le-manoir-de-la-beaume'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Du manoir à Port-de-Terre')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('le-manoir-de-la-beaume'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('port-de-terre'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Port-de-Terre')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('port-de-terre'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Port-de-Terre -> Eotopia')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('port-de-terre'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('eotopia'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Eotopia')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('eotopia'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Eotopia')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('eotopia'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Eotopia -> Mini-ferme du Morvan')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('eotopia'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('mini-ferme-du-morvan'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Mini-ferme du Morvan')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('mini-ferme-du-morvan'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Mini-ferme du Morvan -> Les jardins bénéfiques')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('mini-ferme-du-morvan'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('les-jardins-benefiques'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Les jardins bénéfiques')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('les-jardins-benefiques'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Les jardins bénéfiques')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('les-jardins-benefiques'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Les jardins bénéfiques -> Eden Broc')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('les-jardins-benefiques'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('leden-broc'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Eden Broc -> Viticulteur Delorme')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('leden-broc'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('benoit-delorme'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Viticulteur Delorme -> Ecolieu du portail')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('benoit-delorme'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('ecolieu-du-portail'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Ecolieu du portail')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('ecolieu-du-portail'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Ecolieu du portail -> Vélo qui rit')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('ecolieu-du-portail'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('velo-qui-rit'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Vélo qui rit')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('velo-qui-rit'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Vélo qui rit -> Le champ des Six Reines')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('velo-qui-rit'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('le-champ-des-six-reines'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Le champ des Six Reines')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('le-champ-des-six-reines'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Le champ des Six Reines')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('le-champ-des-six-reines'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Le champ des Six Reines -> Brasserie Plume')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('le-champ-des-six-reines'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('brasserie-plume'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Brasserie Plume')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('brasserie-plume'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Brasserie Plume -> Echel')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('brasserie-plume'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('echel'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Echel')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('echel'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Echel -> Ferme Aymonin')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('echel'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('ferme-aymonin'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Ferme Aymonin -> Eco\'lette')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('ferme-aymonin'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('ecolette'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Eco\'lette')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('ecolette'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Eco\'lette')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('ecolette'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Eco\'lette -> Revis')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('ecolette'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('revis'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Revis')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('revis'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Revis -> Sous la côte')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('revis'), StageAlternativeRelation::DEPARTURE)
            ->addAlternative($this->getAlternative('sous-la-cote'), StageAlternativeRelation::ARRIVAL)
        ;
        yield (new Stage($event))
            ->setName('Sous la côte / Ferme d\'Uzelle')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('sous-la-cote'), StageAlternativeRelation::FULL_DAY)
            ->addAlternative($this->getAlternative('ferme-duzelle'), StageAlternativeRelation::VISIT)
        ;
        yield (new Stage($event))
            ->setName('Sous la côte / Ferme d\'Uzelle')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('sous-la-cote'), StageAlternativeRelation::FULL_DAY)
            ->addAlternative($this->getAlternative('ferme-duzelle'), StageAlternativeRelation::VISIT)
        ;
        yield (new Stage($event))
            ->setName('Sous la côte -> Jardin des potes en ciel')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('sous-la-cote'), StageAlternativeRelation::FULL_DAY)
            ->addAlternative($this->getAlternative('jardin-des-potes-en-ciel'), StageAlternativeRelation::VISIT)
        ;
        yield (new Stage($event))
            ->setName('Jardin des potes en ciel')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('jardin-des-potes-en-ciel'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('Jardin des potes en ciel')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('jardin-des-potes-en-ciel'), StageAlternativeRelation::FULL_DAY)
        ;
        yield (new Stage($event))
            ->setName('After au jardin des potes en ciel')
            ->setType(StageType::AFTER)
            ->setDescription('Rangement !')
            ->addAlternative($this->getAlternative('jardin-des-potes-en-ciel'), StageAlternativeRelation::FULL_DAY)
        ;
    }

    private function generateRegistration(Event $event): Registration
    {
        $stages = $event->getStages()->toArray();

        $start = random_int(0, max(\count($stages) - 5, \count($stages)));
        $stages = \array_slice($stages, $start, $start + 5);

        $registration = new Registration(
            user: $this->getRandomUser(),
            event: $event,
            stages: $stages,
            pricePerDay: $this->faker->numberBetween(10, 70),
        );

        // 2 chances out of 3 to have a confirmed reservation
        if (0 < $this->faker->randomDigit() % 3) {
            $registration->confirm();
        }

        // 1 chances out of 5 to have a canceled reservation
        if (0 === $this->faker->randomDigit() % 5) {
            $registration->cancel();
        }

        return $registration;
    }

    private function getAlternative(string $slug): Alternative
    {
        /** @var Alternative $alternative */
        $alternative = $this->getReference($slug);

        return $alternative;
    }
}
