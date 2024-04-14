<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class FAQQuestion
{
    public string $slug;
    public string $label;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function getQuestion(): Question
    {
        $question = $this->em->getRepository(Question::class)->findOneBySlug($this->slug);

        if (null === $question) {
            throw new \RuntimeException("Question {$this->slug} is not found.");
        }

        return $question;
    }
}
