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
        #[Autowire(env: 'IMAGEKIT_DOMAIN')]
        private string $imageKitDomain,
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
            // In dev environment, we don't use imagekit but directly the remote storage.
            try {
                return $this->imageStorageManipulator->getPath($file);
            } catch (\Exception) {
                return $this->notFoundImageUrl($size, $width, $height, 'Image\\nnon trouvée\\nsur localstack');
            }
        }

        $dimensions = $file->getDimensions();
        $width ??= $dimensions->getWidth($size);
        $height ??= $dimensions->getHeight($size);

        return "{$this->imageKitDomain}/{$file->getPath()}?tr=w-{$width},h-{$height}";
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
