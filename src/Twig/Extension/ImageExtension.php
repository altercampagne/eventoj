<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Twig\Runtime\ImageExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploaded_image_url', [ImageExtensionRuntime::class, 'getUploadedImageUrl']),
            new TwigFunction('image_url', [ImageExtensionRuntime::class, 'getImageUrl']),
        ];
    }
}
