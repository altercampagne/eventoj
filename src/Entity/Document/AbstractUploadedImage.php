<?php

declare(strict_types=1);

namespace App\Entity\Document;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

#[ORM\MappedSuperclass]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
abstract class AbstractUploadedImage
{
    /**
     * This property should be marked as readonly but is not due to a bug in Doctrine.
     *
     * @see https://github.com/doctrine/orm/issues/9863
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private UuidV4 $id;

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

    #[ORM\Column(nullable: true)]
    private ?int $width = null;

    #[ORM\Column(nullable: true)]
    private ?int $height = null;

    #[ORM\Column]
    private readonly \DateTimeImmutable $createdAt;

    public function __construct(
        string $path,
        string $originalFileName,
        ?int $width = null,
        ?int $height = null,
    ) {
        $this->id = new UuidV4();
        $this->path = $path;
        $this->width = $width;
        $this->height = $height;
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

    public function getDimensions(): Dimensions
    {
        return new Dimensions($this->width, $this->height);
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
