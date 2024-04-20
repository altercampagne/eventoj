<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: '`question`')]
#[UniqueEntity(fields: ['slug'], message: 'Il y a déjà une question avec ce slug.')]
#[ORM\Index(name: 'idx_question_slug', fields: ['slug'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Question
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private readonly UuidV4 $id;

    #[ORM\Column(unique: true)]
    #[Gedmo\Slug(fields: ['question'], updatable: false)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 20, enumType: QuestionCategory::class, options: [
        'comment' => 'Category of this question',
    ])]
    private QuestionCategory $category;

    #[Assert\NotNull]
    #[ORM\Column]
    private string $question;

    #[Assert\NotNull]
    #[ORM\Column(type: 'text')]
    private string $answer;

    #[ORM\Column(type: Types::BOOLEAN, options: [
        'comment' => 'Questions used outside of the FAQ are locked and cannot be removed.',
    ])]
    private bool $locked = false;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = new UuidV4();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCategory(): QuestionCategory
    {
        return $this->category;
    }

    public function setCategory(QuestionCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
