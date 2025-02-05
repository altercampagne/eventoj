<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * @return array<string, Question[]>
     */
    public function findAllGroupedByCategories(): array
    {
        $questions = $this->findBy([], ['category' => 'ASC', 'createdAt' => 'ASC']);

        $questionsByCategory = [];
        foreach ($questions as $question) {
            $category = $question->getCategory()->value;

            if (!\array_key_exists($category, $questionsByCategory)) {
                $questionsByCategory[$category] = [];
            }

            $questionsByCategory[$category][] = $question;
        }

        return $questionsByCategory;
    }
}
