<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Document\AbstractUploadedImage;
use App\Service\UploadedImageUrlGenerator;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class UploadedImage
{
    public ?AbstractUploadedImage $file = null;

    /**
     * @var "sm"|"small"|"md"|"medium"|"lg"|"large"
     */
    public string $size = 'md';

    public ?int $width = null;
    public ?int $height = null;

    public function __construct(
        private readonly UploadedImageUrlGenerator $uploadedImageUrlGenerator,
    ) {
    }

    public function getUrl(): string
    {
        return $this->uploadedImageUrlGenerator->getImageUrl($this->file, $this->size, $this->width, $this->height);
    }
}
