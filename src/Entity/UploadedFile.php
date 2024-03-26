<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity]
#[ORM\Table(name: '`uploaded_file`')]
#[UniqueEntity(fields: ['path'], message: 'Il y a déjà un fichier avec ce chemin.')]
#[ORM\Index(name: 'idx_uploaded_file_path', fields: ['path'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class UploadedFile
{
    /**
     * This property should be marked as readonly but is not due to a bug in Doctrine.
     *
     * @see https://github.com/doctrine/orm/issues/9863
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private UuidV4 $id;

    #[ORM\Column(type: 'string', enumType: UploadedFileType::class, options: [
        'comment' => 'Type of this file which correspond to the entity its linked to (event, alternative, ...).',
    ])]
    private readonly UploadedFileType $type;

    #[ORM\Column(options: [
        'comment' => 'This is the path on ObjectStorage.',
    ])]
    private readonly string $path;

    #[ORM\Column]
    private readonly string $originalFileName;

    #[ORM\Column(nullable: true)]
    private ?int $size = null;

    #[ORM\Column(nullable: true)]
    private ?string $mimeType = null;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    public function __construct(UploadedFileType $type, string $path, string $originalFileName)
    {
        $this->id = new UuidV4();
        $this->type = $type;
        $this->path = $path;
        $this->originalFileName = $originalFileName;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getType(): UploadedFileType
    {
        return $this->type;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getOriginalFileName(): string
    {
        return $this->originalFileName;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
