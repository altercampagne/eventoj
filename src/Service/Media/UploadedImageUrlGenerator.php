<?php

declare(strict_types=1);

namespace App\Service\Media;

use App\Entity\Document\AbstractUploadedImage;
use App\Entity\Document\Dimensions;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class UploadedImageUrlGenerator
{
    public function __construct(
        private ImageStorageManipulator $imageStorageManipulator,
        #[Autowire(param: 'kernel.environment')]
        private string $environment,
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
            return $this->notFoundImageUrl($size, $width, $height);
        }

        if ('dev' === $this->environment) {
            // In dev environment, we don't use cloudimg but directly the remote storage.
            try {
                return $this->imageStorageManipulator->getPath($file);
            } catch (\Exception) {
                return $this->notFoundImageUrl($size, $width, $height, 'Image\\nnon trouvée\\nsur localstack');
            }
        }

        $path = "https://{$this->cloudimgToken}.cloudimg.io/{$this->cloudimgAlias}/{$file->getPath()}";
        $dimensions = $file->getDimensions();

        return $path.'?'.http_build_query([
            'width' => $width ?? $dimensions->getWidth($size),
            'height' => $height ?? $dimensions->getHeight($size),
        ]);
    }

    /**
     * @param "sm"|"small"|"md"|"medium"|"lg"|"large" $size
     */
    private function notFoundImageUrl(string $size, ?int $width, ?int $height, string $text = 'Image\nnon trouvée'): string
    {
        $dimensions = new Dimensions();

        $width ??= $dimensions->getWidth($size);
        $height ??= $dimensions->getHeight($size);

        return "https://placehold.co/{$width}x{$height}?text={$text}";
    }
}
