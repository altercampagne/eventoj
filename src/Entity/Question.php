<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ORM\Table(name: '`question`')]
#[UniqueEntity(fields: ['slug'], message: 'Il y a déjà une question avec ce slug.')]
#[ORM\Index(name: 'idx_question_slug', fields: ['slug'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Question
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private readonly Uuid $id;

    #[ORM\Column(unique: true)]
    #[Gedmo\Slug(fields: ['question'], updatable: false)]
    private ?string $slug = null;

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
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getSlug(): ?string
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
