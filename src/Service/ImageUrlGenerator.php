<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class ImageUrlGenerator
{
    public function __construct(
        #[Autowire(env: 'CLOUDIMG_TOKEN')]
        private string $cloudimgToken,
        #[Autowire(env: 'CLOUDIMG_ALIAS')]
        private string $cloudimgAlias,
        #[Autowire(param: 'kernel.environment')]
        private string $environment,
    ) {
    }

    public function getImageUrl(?string $image = null, ?int $width = null, ?int $height = null): string
    {
        if (null === $image) {
            $width = $width ?: 500;
            $height = $height ?: 500;

            return "https://placehold.co/{$width}x{$height}?text=Image\\nnon trouvée";
        }

        // If we're not in production, we don't use cloudimg.io for local images.
        if ('prod' !== $this->environment) {
            if (preg_match('#assets/images#', $image)) {
                return $image;
            }
        }

        $path = "https://{$this->cloudimgToken}.cloudimg.io/{$this->cloudimgAlias}/{$image}";

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
