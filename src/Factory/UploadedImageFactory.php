<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Document\UploadedImage;
use App\Entity\Document\UploadedImageType;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<UploadedImage>
 */
final class UploadedImageFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return UploadedImage::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        /** @var non-falsy-string $originalFileName */
        /* @phpstan-ignore binaryOp.invalid */
        $originalFileName = self::faker()->word().'.'.self::faker()->fileExtension();

        return [
            'path' => 'event/altertour-2023.jpg',
            'type' => self::faker()->randomElement(UploadedImageType::cases()),
            'originalFileName' => $originalFileName,
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(static function (UploadedImage $uploadedImage): void {
                /** @var ?string $mimeType */
                $mimeType = self::faker()->optional()->mimeType();

                $uploadedImage->setSize(self::faker()->optional()->randomDigit());
                $uploadedImage->setMimeType($mimeType);
            })
        ;
    }
}
