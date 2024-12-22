<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Twig\Runtime\UploadedFileExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UploadedFileExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploaded_image_url', [UploadedFileExtensionRuntime::class, 'getImageUrl']),
        ];
    }
}
