<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\QuestionCategory;
use App\Factory\QuestionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class QuestionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Slugs are forced because generation have been improved since they've first been generated.
        QuestionFactory::new()->locked()->createSequence([
            [
                'category' => QuestionCategory::GLOSSARY,
                'question' => "Qu'est-ce qu'un AlterTour ?",
                'answer' => '<div>L\'AlterTour (ou AT) est <strong>un tour participatif à vélo à la rencontre d\'alternatives</strong>.&nbsp;</div><div><br></div><div>Il est accessible sans condition d\'âge ou de niveau sportif, <strong>deux véhicules motorisés nous secondent</strong> pour transporter le matériel logistique (nourriture, cuisine, hygiène ...), vélos et altercyclistes fatigués.&nbsp;</div><div><br>C\'est <strong>un évènement participatif</strong> car chacun·e prend part aux tâches quotidiennes, chacun·e est invité à partager ses talents et chacun·e peut donner son avis et prendre part aux décisions.</div>',
                'slug' => 'quest-ce-quun-altertour',
            ],
            [
                'category' => QuestionCategory::GLOSSARY,
                'question' => 'Qu\'est-ce qu\'une Échappé Belle ?',
                'answer' => '<div>Une échappée belle est un évènement un peu différent de l\'AlterTour.</div>',
                'slug' => 'quest-ce-quune-echappe-belle',
            ],
            [
                'category' => QuestionCategory::GLOSSARY,
                'question' => "Qu'est-ce qu'un Alter-D-Tour ?",
                'answer' => '<div>Un Alter-D-Tour...</div>',
                'slug' => 'quest-ce-quun-alter-d-tour',
            ],
            [
                'category' => QuestionCategory::GLOSSARY,
                'question' => 'Qu\'est-ce qu\'un BièreTour ?',
                'answer' => '<div>Un BièreTour, ...</div>',
                'slug' => 'quest-ce-quun-bieretour',
            ],
            [
                'category' => QuestionCategory::PRICE,
                'question' => 'Pourquoi adhérer à l\'association ?',
                'answer' => '<div>Adhérer c\'est top !</div>',
                'slug' => 'pourquoi-adherer-a-lassociation',
            ],
            [
                'category' => QuestionCategory::STAGES,
                'question' => 'Comment est déterminée la difficulté d\'une étape ?',
                'answer' => '<div>Explications détaillées ici</div>',
                'slug' => 'comment-est-determinee-la-difficulte-dune-etape',
            ],
            [
                'category' => QuestionCategory::CANCELATION,
                'question' => 'Est-ce que je peux annuler ma participation ?',
                'answer' => "<div>L'annulation est possible jusque 15 jours avant le début de l'évènement, dans votre espace Participation.<br><br>Au delà, il faudra que vous trouviez vous-même des personnes susceptibles de vous racheter vos places via la bourse d'échange. Le lien vers la bourse d'échange est disponible sur la page de l'évènement. Il faudra être connecté sur le site pour y accéder.<br><br>En cas de force majeure, envoyer votre demande à inscriptions@altertour.net . Elle sera traitée entre début octobre et fin novembre.</div>",
                'slug' => 'est-ce-que-je-peux-annuler-ma-participation',
            ],
        ]);
    }
}
