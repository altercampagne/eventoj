<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Alternative;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AlternativeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $alternative = new Alternative('Ferme maraîchère Le Champ de l\'Île', <<<EOL
            Installés depuis deux ans à Hérisson, Lisa et Josselin nous ouvre les portes de leur ferme maraîchère bio.

            Ce couple en reconversion est arrivé en mars 2021 à Hérisson, commune accueillante, riche en culture et en dynamiques associatives.
            Leur terrain de quatre hectares, situé au bord de l'Aumance, leur permet aujourd'hui de produire des légumes bios (que nous auront le plaisir de déguster lors de notre passage chez eux !) et accueille un verger très diversifié récemment planté. Le maraîchage sera également entouré de haies fruitières en agroforesterie d'ici l'année prochaine. La ferme s'est inscrite nouvellement dans le réseau Paysan de Nature en tant que Paysans engagés pour la biodiversité.

            Nous aurons la chance et le plaisir d'être accueillis par Lisa et Josselin pendant 3 jours et deux nuits, durant lesquels nous n'auront pas le temps de nous ennuyer ! Le programme définitif n'est pas encore fixé, cependant nous pouvons d'ors et déjà vous promettre de la musique, de la sueur, des échanges divers et variés ainsi que des baignades !
            EOL);
        $manager->persist($alternative);

        $alternative = new Alternative('Solaire 2000', <<<EOL
            Jean-Louis Gaby, ingénieur électromécanicien dans les sous-marins nucléaires à Cherbourg, a quitté son emploi en 1982, pour venir à Tortezais auto-construire sa maison solaire expérimentale, en même temps militer dans l’agriculture bio, les déchets, le solaire, l'éolien, l'anti-nucléaire, et devenir artisan solaire de 2000 à 2009.
            La construction bioclimatique, le solaire passif, actif et l’inertie permettent de ne consommer qu’un stère de bois par an.
            Une installation photovoltaïque de 3 kWc, lui permet de couvrir annuellement la consommation électrique de sa maison, et de sa petite voiture électrique, qui parcourt annuellement 12000 km/an pour des déplacements locaux.
            Jean-Louis nous accueillera lors de notre traversée de l'Allier et nous aurons la chance de découvrir sa maison et d'échanger avec lui autour des sujets qui lui tiennent à cœur.
            EOL);
        $manager->persist($alternative);

        $manager->flush();
    }
}
