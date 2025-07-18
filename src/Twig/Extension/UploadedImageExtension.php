<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\Document\AbstractUploadedImage;
use App\Service\Media\UploadedImageUrlGenerator;
use Twig\Attribute\AsTwigFunction;

class UploadedImageExtension
{
    public function __construct(
        private readonly UploadedImageUrlGenerator $uploadedImageUrlGenerator,
    ) {
    }

    /**
     * @param "sm"|"small"|"md"|"medium"|"lg"|"large" $size
     */
    #[AsTwigFunction('uploaded_image_url')]
    public function getImageUrl(?AbstractUploadedImage $file, string $size = 'md'): string
    {
        return $this->uploadedImageUrlGenerator->getImageUrl($file, $size);
    }
}
