<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\UploadedFile;
use App\Service\UploadedFileUrlGenerator;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class UploadedImage
{
    public ?UploadedFile $file = null;
    public int $width;
    public int $height;

    public function __construct(
        private readonly UploadedFileUrlGenerator $uploadedFileUrlGenerator,
    ) {
    }

    public function getUrl(): string
    {
        if (null === $this->file) {
            return "https://placehold.co/{$this->width}x{$this->height}?text=Image\\nnon trouvée";
        }

        return $this->uploadedFileUrlGenerator->getImageUrl($this->file, $this->width, $this->height);
    }
}
