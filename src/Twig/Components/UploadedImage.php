<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\UploadedFile;
use App\Service\ImageUrlGenerator;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class UploadedImage
{
    public ?UploadedFile $file = null;
    public int $width;
    public int $height;

    public function __construct(
        private readonly ImageUrlGenerator $imageUrlGenerator,
    ) {
    }

    public function getUrl(): string
    {
        return $this->imageUrlGenerator->getImageUrl($this->file?->getPath(), $this->width, $this->height);
    }
}
