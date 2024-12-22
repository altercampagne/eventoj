<?php

declare(strict_types=1);

namespace App\Twig\Runtime;

use App\Entity\UploadedFile;
use App\Service\ImageUrlGenerator;
use Twig\Extension\RuntimeExtensionInterface;

class ImageExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly ImageUrlGenerator $imageUrlGenerator,
    ) {
    }

    public function getUploadedImageUrl(?UploadedFile $file, ?int $width = null, ?int $height = null): string
    {
        return $this->getImageUrl($file?->getPath(), $width, $height);
    }

    public function getImageUrl(?string $image, ?int $width = null, ?int $height = null): string
    {
        return $this->imageUrlGenerator->getImageUrl($image, $width, $height);
    }
}
