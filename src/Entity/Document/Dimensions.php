<?php

declare(strict_types=1);

namespace App\Entity\Document;

/**
 * Availables sizes (default size for landscape):
 * - sm (320x240)
 * - md (640x480)
 * - lg (1568x1176)
 */
final readonly class Dimensions
{
    public function __construct(
        private readonly ?int $originalWidth = null,
        private readonly ?int $originalHeight = null,
    ) {
    }

    public function isLandscape(): bool
    {
        if (null === $this->originalWidth || null === $this->originalHeight) {
            return true;
        }

        return $this->originalWidth > $this->originalHeight;
    }

    public function isPortrait(): bool
    {
        if (null === $this->originalWidth || null === $this->originalHeight) {
            return false;
        }

        return $this->originalWidth < $this->originalHeight;
    }

    /**
     * @param "sm"|"small"|"md"|"medium"|"lg"|"large" $size
     */
    public function getWidth(string $size): int
    {
        return match ($size) {
            'sm', 'small' => $this->getWidthSmall(),
            'md', 'medium' => $this->getWidthMedium(),
            'lg', 'large' => $this->getWidthLarge(),
        };
    }

    /**
     * @param "sm"|"small"|"md"|"medium"|"lg"|"large" $size
     */
    public function getHeight(string $size): int
    {
        return match ($size) {
            'sm', 'small' => $this->getHeightSmall(),
            'md', 'medium' => $this->getHeightMedium(),
            'lg', 'large' => $this->getHeightLarge(),
        };
    }

    public function getWidthSmall(): int
    {
        return $this->isLandscape() ? 320 : 240;
    }

    public function getHeightSmall(): int
    {
        return $this->isLandscape() ? 240 : 320;
    }

    public function getWidthMedium(): int
    {
        return $this->isLandscape() ? 640 : 480;
    }

    public function getHeightMedium(): int
    {
        return $this->isLandscape() ? 480 : 640;
    }

    public function getWidthLarge(): int
    {
        return $this->isLandscape() ? 1568 : 1176;
    }

    public function getHeightLarge(): int
    {
        return $this->isLandscape() ? 1176 : 1568;
    }
}
