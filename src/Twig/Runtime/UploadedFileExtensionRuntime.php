<?php

declare(strict_types=1);

namespace App\Twig\Runtime;

use App\Entity\UploadedFile;
use App\Service\UploadedFileUrlGenerator;
use Twig\Extension\RuntimeExtensionInterface;

class UploadedFileExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly UploadedFileUrlGenerator $uploadedFileUrlGenerator,
    ) {
    }

    public function getImageUrl(?UploadedFile $file, ?int $width = null, ?int $height = null): string
    {
        return $this->uploadedFileUrlGenerator->getImageUrl($file, $width, $height);
    }
}
