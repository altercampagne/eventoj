<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Util\ReflectionHelper;
use App\Entity\Question;
use App\Entity\QuestionCategory;
use Doctrine\Persistence\ObjectManager;

class QuestionFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getQuestions() as $question) {
            $manager->persist($question);
        }

        $manager->flush();
    }

    /**
     * @return iterable<Question>
     */
    private function getQuestions(): iterable
    {
        $question = new Question();
        $question
            ->setCategory(QuestionCategory::GLOSSARY)
            ->setQuestion('Qu\'est-ce qu\'un AlterTour ?')
            ->setAnswer('<div>L\'AlterTour (ou AT) est <strong>un tour participatif à vélo à la rencontre d\'alternatives</strong>.&nbsp;</div><div><br></div><div>Il est accessible sans condition d\'âge ou de niveau sportif, <strong>deux véhicules motorisés nous secondent</strong> pour transporter le matériel logistique (nourriture, cuisine, hygiène ...), vélos et altercyclistes fatigués.&nbsp;</div><div><br>C\'est <strong>un évènement participatif</strong> car chacun·e prend part aux tâches quotidiennes, chacun·e est invité à partager ses talents et chacun·e peut donner son avis et prendre part aux décisions.</div>')
        ;
        ReflectionHelper::setProperty($question, 'locked', true);

        yield $question;

        $question = new Question();
        $question
            ->setCategory(QuestionCategory::GLOSSARY)
            ->setQuestion('Qu\'est-ce qu\'une Échappé Belle ?')
            ->setAnswer('<div>Une échappée belle est un évènement un peu différent de l\'AlterTour.</div>')
        ;
        ReflectionHelper::setProperty($question, 'locked', true);

        yield $question;

        $question = new Question();
        $question
            ->setCategory(QuestionCategory::GLOSSARY)
            ->setQuestion('Qu\'est-ce qu\'un Alter-D-Tour ?')
            ->setAnswer('<div>Un Alter-D-Tour...</div>')
        ;
        ReflectionHelper::setProperty($question, 'locked', true);

        yield $question;

        $question = new Question();
        $question
            ->setCategory(QuestionCategory::GLOSSARY)
            ->setQuestion('Qu\'est-ce qu\'un BièreTour ?')
            ->setAnswer('<div>Un BièreTour, ...</div>')
        ;
        ReflectionHelper::setProperty($question, 'locked', true);

        yield $question;

        $question = new Question();
        $question
            ->setCategory(QuestionCategory::PRICE)
            ->setQuestion('Pourquoi adhérer à l\'association ?')
            ->setAnswer('<div>Adhérer c\{est top !</div>')
        ;
        ReflectionHelper::setProperty($question, 'locked', true);

        yield $question;
    }
}
