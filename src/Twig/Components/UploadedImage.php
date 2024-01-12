<?php

declare(strict_types=1);

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class UploadedImage
{
    public string $path;
    public int $width;
    public int $height;

    public function __construct(
        private readonly string $cloudimgToken,
        private readonly string $cloudimgAlias,
    ) {
    }

    public function getUrl(): string
    {
        return "https://{$this->cloudimgToken}.cloudimg.io/{$this->cloudimgAlias}/{$this->path}?width={$this->width}&height={$this->height}";
    }
}
