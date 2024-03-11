<?php

declare(strict_types=1);

namespace App\DataFixtures\Events\AT2023;

use App\DataFixtures\AbstractFixture;
use App\DataFixtures\AlternativeFixtures;
use App\DataFixtures\Util\FixtureBuilder;
use App\Entity\Alternative;
use App\Entity\Event;
use App\Entity\Membership;
use App\Entity\Payment;
use App\Entity\Registration;
use App\Entity\RegistrationStatus;
use App\Entity\Stage;
use App\Entity\StageRegistration;
use App\Entity\StageType;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [AlternativeFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $picture = FixtureBuilder::createUploadedFile(path: 'event/altertour-2023.jpg');
        $manager->persist($picture);

        $event = Event::AT();
        $event
            ->setName('AlterTour 2023')
            ->setPublishedAt(new \DateTimeImmutable('2023-02-01'))
            ->setOpeningDateForBookings(new \DateTimeImmutable('2023-05-01'))
            ->setDescription(<<<END
                Cet été, l’AlterTour roulera du 10 juillet au 19 août, de Montluçon (03) à Besançon (25).
                Ce sera la 16e édition ! 🥳
                END)
            ->setPicture($picture)
        ;

        $startDate = new \DateTime('2023-07-08');
        foreach ($this->getStages($event) as $stage) {
            $event->addStage($stage);
            $stage->setDate(\DateTimeImmutable::createFromMutable($startDate));

            $manager->persist($stage);

            $startDate->modify('+1 day');
        }

        $manager->persist($event);

        for ($i = 0; $i < 100; ++$i) {
            $user = FixtureBuilder::createUser();
            $manager->persist($user);

            $registration = $this->generateRegistration($event, $user);
            $manager->persist($registration);

            $payment = new Payment(
                payer: $user,
                amount: $this->getFaker()->numberBetween(10, 70) * 100,
                registration: $registration,
            );
            $payment->approve();
            $manager->persist($payment);

            $membership = Membership::createForUser($user, $payment);
            $manager->persist($membership);
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
            ->setDescription("<div>08h00 - 09h00 : Réveil, petit déjeuner<br>09h00 - 12h00 : On s'affaire on s'affaire<br>12h00 - 14h00 : Repas partagé avec l'association Cyclopède<br>14h00 - 17h00 : On s'attelle aux derniers préparatifs avant le démarrage du tour<br>17h00 - 18h30 : Conférence gesticulée de Mathieu Dalmais<br>18h30 - 19h00 : Accueil des nouvelleaux<br>19h00 - 19h30 : Présentation de la journée du lendemain et répartition des rôles<br>19h30 - 21h00 : Repas<br>21h00 - 23h00 : Soirée</div>")
            ->addAlternative($this->getAlternative('le-champ-des-possibles'))
        ;
        yield (new Stage($event))
            ->setName('Le champ des possibles')
            ->setDescription('<div>08h00 - 09h00 : Réveil, petit déjeuner<br>09h00 - 12h00 : Activités avec les accueillant·es<br>12h00 - 14h00 : Repas<br>14h00 - 16h00 : Activités libres, accueil des nouvelleaux<br>16h00 - 17h00 : Présentation de la journée du lendemain et répartition des rôles<br>17h00 - 18h00 : Temps de présentation du collectif du Champ des possibles et de l\'AlterTour<br>18h00 - 20h00 : Concert de Yves Vessiere<br>20h00 - 22h00 : Repas')
            ->addAlternative($this->getAlternative('le-champ-des-possibles'))
        ;
        yield (new Stage($event))
            ->setName('De Montluçon à Hérisson')
            ->setDescription('De Montluçon à Hérisson pour rendre visite à Josselin & Lisa.')
            ->addAlternative($this->getAlternative('le-champ-des-possibles'))
            ->addAlternative($this->getAlternative('les-champs-de-lile'))
        ;
        yield (new Stage($event))
            ->setName('Journée chantiers à Hérisson')
            ->setDescription('On file un coup de main à Josselin & Lisa !')
            ->addAlternative($this->getAlternative('les-champs-de-lile'))
        ;
        yield (new Stage($event))
            ->setName('De Hérisson à Tortezais')
            ->setDescription('On va rendre visite à Jean-Louis Gaby')
            ->addAlternative($this->getAlternative('les-champs-de-lile'))
            ->addAlternative($this->getAlternative('solaire-2000'))
        ;
        yield (new Stage($event))
            ->setName('De Tortezais au manois de la Beaume')
            ->setDescription('Hop, ça continue !')
            ->addAlternative($this->getAlternative('solaire-2000'))
            ->addAlternative($this->getAlternative('le-manoir-de-la-beaume'))
        ;
        yield (new Stage($event))
            ->setName('On reste au manoir')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('le-manoir-de-la-beaume'))
        ;
        yield (new Stage($event))
            ->setName('On reste au manoir')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('le-manoir-de-la-beaume'))
        ;
        yield (new Stage($event))
            ->setName('Du manoir à Port-de-Terre')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('le-manoir-de-la-beaume'))
            ->addAlternative($this->getAlternative('port-de-terre'))
        ;
        yield (new Stage($event))
            ->setName('Port-de-Terre')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('port-de-terre'))
        ;
        yield (new Stage($event))
            ->setName('Port-de-Terre -> Eotopia')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('port-de-terre'))
            ->addAlternative($this->getAlternative('eotopia'))
        ;
        yield (new Stage($event))
            ->setName('Eotopia')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('eotopia'))
        ;
        yield (new Stage($event))
            ->setName('Eotopia')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('eotopia'))
        ;
        yield (new Stage($event))
            ->setName('Eotopia -> Mini-ferme du Morvan')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('eotopia'))
            ->addAlternative($this->getAlternative('mini-ferme-du-morvan'))
        ;
        yield (new Stage($event))
            ->setName('Mini-ferme du Morvan')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('mini-ferme-du-morvan'))
        ;
        yield (new Stage($event))
            ->setName('Mini-ferme du Morvan -> Les jardins bénéfiques')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('mini-ferme-du-morvan'))
            ->addAlternative($this->getAlternative('les-jardins-benefiques'))
        ;
        yield (new Stage($event))
            ->setName('Les jardins bénéfiques')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('les-jardins-benefiques'))
        ;
        yield (new Stage($event))
            ->setName('Les jardins bénéfiques')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('les-jardins-benefiques'))
        ;
        yield (new Stage($event))
            ->setName('Les jardins bénéfiques -> Eden Broc')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('les-jardins-benefiques'))
            ->addAlternative($this->getAlternative('leden-broc'))
        ;
        yield (new Stage($event))
            ->setName('Eden Broc -> Viticulteur Delorme')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('leden-broc'))
            ->addAlternative($this->getAlternative('benoit-delorme'))
        ;
        yield (new Stage($event))
            ->setName('Viticulteur Delorme -> Ecolieu du portail')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('benoit-delorme'))
            ->addAlternative($this->getAlternative('ecolieu-du-portail'))
        ;
        yield (new Stage($event))
            ->setName('Ecolieu du portail')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('ecolieu-du-portail'))
        ;
        yield (new Stage($event))
            ->setName('Ecolieu du portail -> Vélo qui rit')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('ecolieu-du-portail'))
            ->addAlternative($this->getAlternative('velo-qui-rit'))
        ;
        yield (new Stage($event))
            ->setName('Vélo qui rit')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('velo-qui-rit'))
        ;
        yield (new Stage($event))
            ->setName('Vélo qui rit -> Le champ des Six Reines')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('velo-qui-rit'))
            ->addAlternative($this->getAlternative('le-champ-des-six-reines'))
        ;
        yield (new Stage($event))
            ->setName('Le champ des Six Reines')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('le-champ-des-six-reines'))
        ;
        yield (new Stage($event))
            ->setName('Le champ des Six Reines')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('le-champ-des-six-reines'))
        ;
        yield (new Stage($event))
            ->setName('Le champ des Six Reines -> Brasserie Plume')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('le-champ-des-six-reines'))
            ->addAlternative($this->getAlternative('brasserie-plume'))
        ;
        yield (new Stage($event))
            ->setName('Brasserie Plume')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('brasserie-plume'))
        ;
        yield (new Stage($event))
            ->setName('Brasserie Plume -> Echel')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('brasserie-plume'))
            ->addAlternative($this->getAlternative('echel'))
        ;
        yield (new Stage($event))
            ->setName('Echel')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('echel'))
        ;
        yield (new Stage($event))
            ->setName('Echel -> Ferme Aymonin')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('echel'))
            ->addAlternative($this->getAlternative('ferme-aymonin'))
        ;
        yield (new Stage($event))
            ->setName('Ferme Aymonin -> Eco\'lette')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('ferme-aymonin'))
            ->addAlternative($this->getAlternative('ecolette'))
        ;
        yield (new Stage($event))
            ->setName('Eco\'lette')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('ecolette'))
        ;
        yield (new Stage($event))
            ->setName('Eco\'lette')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('ecolette'))
        ;
        yield (new Stage($event))
            ->setName('Eco\'lette -> Revis')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('ecolette'))
            ->addAlternative($this->getAlternative('revis'))
        ;
        yield (new Stage($event))
            ->setName('Revis')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('revis'))
        ;
        yield (new Stage($event))
            ->setName('Revis -> Sous la côte')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('revis'))
            ->addAlternative($this->getAlternative('sous-la-cote'))
        ;
        yield (new Stage($event))
            ->setName('Sous la côte / Ferme d\'Uzelle')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('sous-la-cote'))
            ->addAlternative($this->getAlternative('ferme-duzelle'))
        ;
        yield (new Stage($event))
            ->setName('Sous la côte / Ferme d\'Uzelle')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('sous-la-cote'))
            ->addAlternative($this->getAlternative('ferme-duzelle'))
        ;
        yield (new Stage($event))
            ->setName('Sous la côte -> Jardin des potes en ciel')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('sous-la-cote'))
            ->addAlternative($this->getAlternative('jardin-des-potes-en-ciel'))
        ;
        yield (new Stage($event))
            ->setName('Jardin des potes en ciel')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('jardin-des-potes-en-ciel'))
        ;
        yield (new Stage($event))
            ->setName('Jardin des potes en ciel')
            ->setDescription('A COMPLETER')
            ->addAlternative($this->getAlternative('jardin-des-potes-en-ciel'))
        ;
        yield (new Stage($event))
            ->setName('After au jardin des potes en ciel')
            ->setType(StageType::AFTER)
            ->setDescription('Rangement !')
            ->addAlternative($this->getAlternative('jardin-des-potes-en-ciel'))
        ;
    }

    private function generateRegistration(Event $event, User $user): Registration
    {
        $registration = new Registration(
            user: $user,
            event: $event,
        );

        $stages = $event->getStages()->toArray();
        if (5 > \count($stages)) {
            throw new \RuntimeException('Not enough stages in event to generate registrations');
        }

        $start = random_int(0, \count($stages) - 5);
        $stages = \array_slice($stages, $start, $start + 5);
        $stagesRegistrations = array_map(function (Stage $stage) use ($registration): StageRegistration {
            return new StageRegistration(stage: $stage, registration: $registration);
        }, $stages);

        $registration
            ->setStagesRegistrations($stagesRegistrations)
            ->setPrice($this->getFaker()->numberBetween(10, 70) * 100 * \count($stages))
            ->setNeededBike($this->getFaker()->boolean() ? 1 : 0)
        ;

        // 2 chances out of 3 to have a confirmed reservation
        if (0 < $this->getFaker()->randomDigit() % 3) {
            (new \ReflectionProperty(Registration::class, 'status'))->setValue($registration, RegistrationStatus::CONFIRMED);
            (new \ReflectionProperty(Registration::class, 'confirmedAt'))->setValue($registration, new \DateTimeImmutable());
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
