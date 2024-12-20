<?php

declare(strict_types=1);

namespace App\Story;

use App\Entity\Alternative;
use App\Entity\Registration;
use App\Entity\RegistrationStatus;
use App\Entity\StageType;
use App\Factory\EventFactory;
use App\Factory\MembershipFactory;
use App\Factory\PaymentFactory;
use App\Factory\RegistrationFactory;
use App\Factory\StageFactory;
use App\Factory\UserFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zenstruck\Foundry\Story;

final class AlterTour2023Story extends Story
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function build(): void
    {
        $alternatives = [];
        $rawAlternatives = $this->serializer->deserialize(file_get_contents(__DIR__.'/alternatives2023.yaml'), Alternative::class.'[]', 'yaml');
        foreach ($rawAlternatives as $alternative) {
            $this->em->persist($alternative);
        }
        $this->em->flush();
        foreach ($rawAlternatives as $alternative) {
            $alternatives[$alternative->getSlug()] = $alternative;
        }

        $event = EventFactory::new()->create([
            'name' => 'AlterTour 2023',
            'publishedAt' => new \DateTimeImmutable('2023-02-01'),
            'openingDateForBookings' => new \DateTimeImmutable('2023-05-01'),
            'description' => <<<END
                Cet été, l’AlterTour roulera du 10 juillet au 19 août, de Montluçon (03) à Besançon (25).
                Ce sera la 16e édition ! 🥳
                END,
            'pahekoProjectId' => 1,
        ])->_real();

        StageFactory::createSequence(function () use ($event, $alternatives): iterable {
            $startDate = new \DateTime('2023-07-09');

            foreach ($this->getStages() as $stage) {
                $stageAlternatives = new ArrayCollection();
                foreach ($stage['alternatives'] as $slug) {
                    $stageAlternatives->add($alternatives[$slug]);
                }

                yield array_merge($stage, [
                    'event' => $event,
                    'date' => \DateTimeImmutable::createFromMutable($startDate),
                    'alternatives' => $stageAlternatives,
                ]);

                $startDate->modify('+1 day');
            }
        });

        for ($i = 0; $i < 100; ++$i) {
            $user = UserFactory::createOne()->_real();

            $registration = RegistrationFactory::new()->withStagesRegistrations()->create([
                'user' => $user,
                'event' => $event,
            ])->_real();

            // 2 chances out of 3 to have a confirmed reservation
            if (0 < random_int(1, 3) % 3) {
                (new \ReflectionProperty(Registration::class, 'status'))->setValue($registration, RegistrationStatus::CONFIRMED);
                (new \ReflectionProperty(Registration::class, 'confirmedAt'))->setValue($registration, new \DateTimeImmutable());
            }

            MembershipFactory::createOne([
                'user' => $user,
                'payment' => PaymentFactory::new()->approved()->create([
                    'payer' => $user,
                    'registration' => $registration,
                ]),
            ]);
        }
    }

    /**
     * @return iterable<array{name: string, description: string, alternatives: string[], type?: StageType}>
     */
    public function getStages(): iterable
    {
        yield [
            'name' => 'Le champ des possibles',
            'description' => '<div>08h00 - 09h00 : Réveil, petit déjeuner<br>09h00 - 12h00 : Activités avec les accueillant·es<br>12h00 - 14h00 : Repas<br>14h00 - 16h00 : Activités libres, accueil des nouvelleaux<br>16h00 - 17h00 : Présentation de la journée du lendemain et répartition des rôles<br>17h00 - 18h00 : Temps de présentation du collectif du Champ des possibles et de l\'AlterTour<br>18h00 - 20h00 : Concert de Yves Vessiere<br>20h00 - 22h00 : Repas',
            'alternatives' => ['le-champ-des-possibles'],
        ];
        yield [
            'name' => 'De Montluçon à Hérisson',
            'description' => 'De Montluçon à Hérisson pour rendre visite à Josselin & Lisa.',
            'alternatives' => ['le-champ-des-possibles', 'les-champs-de-lile'],
        ];
        yield [
            'name' => 'Journée chantiers à Hérisson',
            'description' => 'On file un coup de main à Josselin & Lisa !',
            'alternatives' => ['les-champs-de-lile'],
        ];
        yield [
            'name' => 'De Hérisson à Tortezais',
            'description' => 'On va rendre visite à Jean-Louis Gaby',
            'alternatives' => ['les-champs-de-lile', 'solaire-2000'],
        ];
        yield [
            'name' => 'De Tortezais au manois de la Beaume',
            'description' => 'Hop, ça continue !',
            'alternatives' => ['solaire-2000', 'le-manoir-de-la-beaume'],
        ];
        yield [
            'name' => 'On reste au manoir',
            'description' => 'A COMPLETER',
            'alternatives' => ['le-manoir-de-la-beaume'],
        ];
        yield [
            'name' => 'On reste au manoir',
            'description' => 'A COMPLETER',
            'alternatives' => ['le-manoir-de-la-beaume'],
        ];
        yield [
            'name' => 'Du manoir à Port-de-Terre',
            'description' => 'A COMPLETER',
            'alternatives' => ['le-manoir-de-la-beaume', 'port-de-terre'],
        ];
        yield [
            'name' => 'Port-de-Terre',
            'description' => 'A COMPLETER',
            'alternatives' => ['port-de-terre'],
        ];
        yield [
            'name' => 'Port-de-Terre -> Eotopia',
            'description' => 'A COMPLETER',
            'alternatives' => ['port-de-terre', 'eotopia'],
        ];
        yield [
            'name' => 'Eotopia',
            'description' => 'A COMPLETER',
            'alternatives' => ['eotopia'],
        ];
        yield [
            'name' => 'Eotopia',
            'description' => 'A COMPLETER',
            'alternatives' => ['eotopia'],
        ];
        yield [
            'name' => 'Eotopia -> Mini-ferme du Morvan',
            'description' => 'A COMPLETER',
            'alternatives' => ['eotopia', 'mini-ferme-du-morvan'],
        ];
        yield [
            'name' => 'Mini-ferme du Morvan',
            'description' => 'A COMPLETER',
            'alternatives' => ['mini-ferme-du-morvan'],
        ];
        yield [
            'name' => 'Mini-ferme du Morvan -> Les jardins bénéfiques',
            'description' => 'A COMPLETER',
            'alternatives' => ['mini-ferme-du-morvan', 'les-jardins-benefiques'],
        ];
        yield [
            'name' => 'Les jardins bénéfiques',
            'description' => 'A COMPLETER',
            'alternatives' => ['les-jardins-benefiques'],
        ];
        yield [
            'name' => 'Les jardins bénéfiques',
            'description' => 'A COMPLETER',
            'alternatives' => ['les-jardins-benefiques'],
        ];
        yield [
            'name' => 'Les jardins bénéfiques -> Eden Broc',
            'description' => 'A COMPLETER',
            'alternatives' => ['les-jardins-benefiques', 'leden-broc'],
        ];
        yield [
            'name' => 'Eden Broc -> Viticulteur Delorme',
            'description' => 'A COMPLETER',
            'alternatives' => ['leden-broc', 'benoit-delorme'],
        ];
        yield [
            'name' => 'Viticulteur Delorme -> Ecolieu du portail',
            'description' => 'A COMPLETER',
            'alternatives' => ['benoit-delorme', 'ecolieu-du-portail'],
        ];
        yield [
            'name' => 'Ecolieu du portail',
            'description' => 'A COMPLETER',
            'alternatives' => ['ecolieu-du-portail'],
        ];
        yield [
            'name' => 'Ecolieu du portail -> Vélo qui rit',
            'description' => 'A COMPLETER',
            'alternatives' => ['ecolieu-du-portail', 'velo-qui-rit'],
        ];
        yield [
            'name' => 'Vélo qui rit',
            'description' => 'A COMPLETER',
            'alternatives' => ['velo-qui-rit'],
        ];
        yield [
            'name' => 'Vélo qui rit -> Le champ des Six Reines',
            'description' => 'A COMPLETER',
            'alternatives' => ['velo-qui-rit', 'le-champ-des-six-reines'],
        ];
        yield [
            'name' => 'Le champ des Six Reines',
            'description' => 'A COMPLETER',
            'alternatives' => ['le-champ-des-six-reines'],
        ];
        yield [
            'name' => 'Le champ des Six Reines',
            'description' => 'A COMPLETER',
            'alternatives' => ['le-champ-des-six-reines'],
        ];
        yield [
            'name' => 'Le champ des Six Reines -> Brasserie Plume',
            'description' => 'A COMPLETER',
            'alternatives' => ['le-champ-des-six-reines', 'brasserie-plume'],
        ];
        yield [
            'name' => 'Brasserie Plume',
            'description' => 'A COMPLETER',
            'alternatives' => ['brasserie-plume'],
        ];
        yield [
            'name' => 'Brasserie Plume -> Echel',
            'description' => 'A COMPLETER',
            'alternatives' => ['brasserie-plume', 'echel'],
        ];
        yield [
            'name' => 'Echel',
            'description' => 'A COMPLETER',
            'alternatives' => ['echel'],
        ];
        yield [
            'name' => 'Echel -> Ferme Aymonin',
            'description' => 'A COMPLETER',
            'alternatives' => ['echel', 'ferme-aymonin'],
        ];
        yield [
            'name' => 'Ferme Aymonin -> Eco\'lette',
            'description' => 'A COMPLETER',
            'alternatives' => ['ferme-aymonin', 'ecolette'],
        ];
        yield [
            'name' => 'Eco\'lette',
            'description' => 'A COMPLETER',
            'alternatives' => ['ecolette'],
        ];
        yield [
            'name' => 'Eco\'lette',
            'description' => 'A COMPLETER',
            'alternatives' => ['ecolette'],
        ];
        yield [
            'name' => 'Eco\'lette -> Revis',
            'description' => 'A COMPLETER',
            'alternatives' => ['ecolette', 'revis'],
        ];
        yield [
            'name' => 'Revis',
            'description' => 'A COMPLETER',
            'alternatives' => ['revis'],
        ];
        yield [
            'name' => 'Revis -> Sous la côte',
            'description' => 'A COMPLETER',
            'alternatives' => ['revis', 'sous-la-cote'],
        ];
        yield [
            'name' => 'Sous la côte / Ferme d\'Uzelle',
            'description' => 'A COMPLETER',
            'alternatives' => ['sous-la-cote', 'ferme-duzelle'],
        ];
        yield [
            'name' => 'Sous la côte / Ferme d\'Uzelle',
            'description' => 'A COMPLETER',
            'alternatives' => ['sous-la-cote', 'ferme-duzelle'],
        ];
        yield [
            'name' => 'Sous la côte -> Jardin des potes en ciel',
            'description' => 'A COMPLETER',
            'alternatives' => ['sous-la-cote', 'jardin-des-potes-en-ciel'],
        ];
        yield [
            'name' => 'Jardin des potes en ciel',
            'description' => 'A COMPLETER',
            'alternatives' => ['jardin-des-potes-en-ciel'],
        ];
        yield [
            'name' => 'Jardin des potes en ciel',
            'description' => 'A COMPLETER',
            'alternatives' => ['jardin-des-potes-en-ciel'],
        ];
        yield [
            'name' => 'After au jardin des potes en ciel',
            'type' => StageType::AFTER,
            'description' => 'Rangement !',
            'alternatives' => ['jardin-des-potes-en-ciel'],
        ];
    }
}
