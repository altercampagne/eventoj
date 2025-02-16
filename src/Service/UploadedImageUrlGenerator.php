<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Document\AbstractUploadedImage;
use App\Entity\Document\Dimensions;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class UploadedImageUrlGenerator
{
    public function __construct(
        #[Autowire(env: 'CLOUDIMG_TOKEN')]
        private string $cloudimgToken,
        #[Autowire(env: 'CLOUDIMG_ALIAS')]
        private string $cloudimgAlias,
    ) {
    }

    /**
     * @param "sm"|"small"|"md"|"medium"|"lg"|"large" $size
     */
    public function getImageUrl(?AbstractUploadedImage $file, string $size = 'md', ?int $width = null, ?int $height = null): string
    {
        if (null === $file) {
            $dimensions = new Dimensions();

            $width ??= $dimensions->getWidth($size);
            $height ??= $dimensions->getHeight($size);

            return "https://placehold.co/{$width}x{$height}?text=Image\\nnon trouvée";
        }

        $path = "https://{$this->cloudimgToken}.cloudimg.io/{$this->cloudimgAlias}/{$file->getPath()}";
        $dimensions = $file->getDimensions();

        return $path.'?'.http_build_query([
            'width' => $width ?? $dimensions->getWidth($size),
            'height' => $height ?? $dimensions->getHeight($size),
        ]);
    }
}
