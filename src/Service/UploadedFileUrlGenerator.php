<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UploadedFile;

final readonly class UploadedFileUrlGenerator
{
    public function __construct(
        private string $cloudimgToken,
        private string $cloudimgAlias,
    ) {
    }

    public function getImageUrl(UploadedFile $file, int $width, int $height): string
    {
        return "https://{$this->cloudimgToken}.cloudimg.io/{$this->cloudimgAlias}/{$file->getPath()}?width={$width}&height={$height}";
    }
}
