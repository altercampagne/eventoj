<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\UploadedFile;
use App\Entity\UploadedFileType;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<UploadedFile>
 */
final class UploadedFileFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return UploadedFile::class;
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
            'type' => self::faker()->randomElement(UploadedFileType::cases()),
            'originalFileName' => $originalFileName,
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(static function (UploadedFile $uploadedFile): void {
                /** @var ?string $mimeType */
                $mimeType = self::faker()->optional()->mimeType();

                $uploadedFile->setSize(self::faker()->optional()->randomDigit());
                $uploadedFile->setMimeType($mimeType);
            })
        ;
    }
}
