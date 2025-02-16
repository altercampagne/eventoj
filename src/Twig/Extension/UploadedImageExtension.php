<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Twig\Runtime\UploadedImageExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UploadedImageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploaded_image_url', [UploadedImageExtensionRuntime::class, 'getImageUrl']),
        ];
    }
}
