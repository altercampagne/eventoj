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

    public function getImageUrl(?UploadedFile $file, ?int $width = null, ?int $height = null): string
    {
        if (null === $file) {
            $width = $width ?: 500;
            $height = $height ?: 500;

            return "https://placehold.co/{$width}x{$height}?text=Image\\nnon trouvée";
        }

        $path = "https://{$this->cloudimgToken}.cloudimg.io/{$this->cloudimgAlias}/{$file->getPath()}";

        $parameters = [];
        if (null !== $width) {
            $parameters['width'] = $width;
        }
        if (null !== $height) {
            $parameters['height'] = $height;
        }

        if (0 === \count($parameters)) {
            return $path;
        }

        return $path.'?'.http_build_query($parameters);
    }
}
