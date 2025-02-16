<?php

declare(strict_types=1);

namespace App\Twig\Runtime;

use App\Entity\Document\AbstractUploadedImage;
use App\Service\UploadedImageUrlGenerator;
use Twig\Extension\RuntimeExtensionInterface;

class UploadedImageExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly UploadedImageUrlGenerator $uploadedImageUrlGenerator,
    ) {
    }

    /**
     * @param "sm"|"small"|"md"|"medium"|"lg"|"large" $size
     */
    public function getImageUrl(?AbstractUploadedImage $file, string $size = 'md'): string
    {
        return $this->uploadedImageUrlGenerator->getImageUrl($file, $size);
    }
}
