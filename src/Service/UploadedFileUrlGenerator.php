<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UploadedFile;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class UploadedFileUrlGenerator
{
    public function __construct(
        #[Autowire(env: 'CLOUDIMG_TOKEN')]
        private string $cloudimgToken,
        #[Autowire(env: 'CLOUDIMG_ALIAS')]
        private string $cloudimgAlias,
    ) {
    }

    public function getImageUrl(UploadedFile $file, int $width, int $height): string
    {
        return "https://{$this->cloudimgToken}.cloudimg.io/{$this->cloudimgAlias}/{$file->getPath()}?width={$width}&height={$height}";
    }
}
