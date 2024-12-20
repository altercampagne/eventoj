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
        QuestionFactory::new()->locked()->createSequence([
            [
                'category' => QuestionCategory::GLOSSARY,
                'question' => 'Qu\'est-ce qu\'un AlterTour ?',
                'answer' => '<div>L\'AlterTour (ou AT) est <strong>un tour participatif à vélo à la rencontre d\'alternatives</strong>.&nbsp;</div><div><br></div><div>Il est accessible sans condition d\'âge ou de niveau sportif, <strong>deux véhicules motorisés nous secondent</strong> pour transporter le matériel logistique (nourriture, cuisine, hygiène ...), vélos et altercyclistes fatigués.&nbsp;</div><div><br>C\'est <strong>un évènement participatif</strong> car chacun·e prend part aux tâches quotidiennes, chacun·e est invité à partager ses talents et chacun·e peut donner son avis et prendre part aux décisions.</div>',
            ],
            [
                'category' => QuestionCategory::GLOSSARY,
                'question' => 'Qu\'est-ce qu\'une Échappé Belle ?',
                'answer' => '<div>Une échappée belle est un évènement un peu différent de l\'AlterTour.</div>',
            ],
            [
                'category' => QuestionCategory::GLOSSARY,
                'question' => 'Qu\'est-ce qu\'un Alter-D-Tour ?',
                'answer' => '<div>Un Alter-D-Tour...</div>',
            ],
            [
                'category' => QuestionCategory::GLOSSARY,
                'question' => 'Qu\'est-ce qu\'un BièreTour ?',
                'answer' => '<div>Un BièreTour, ...</div>',
            ],
            [
                'category' => QuestionCategory::PRICE,
                'question' => 'Pourquoi adhérer à l\'association ?',
                'answer' => '<div>Adhérer c\'est top !</div>',
            ],
            [
                'category' => QuestionCategory::STAGES,
                'question' => 'Comment est déterminée la difficulté d\'une étape ?',
                'answer' => '<div>Explications détaillées ici</div>',
            ],
        ]);
    }
}
