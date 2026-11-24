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

    private ?Question $question = null;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function getQuestion(): Question
    {
        if ($this->question instanceof Question) {
            return $this->question;
        }

        $this->question = $this->em->getRepository(Question::class)->findOneBySlug($this->slug);

        if (!$this->question instanceof Question) {
            throw new \RuntimeException("Question {$this->slug} is not found.");
        }

        return $this->question;
    }
}
