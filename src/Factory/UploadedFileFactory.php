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

    protected function defaults(): array|callable
    {
        return [
            'path' => 'event/altertour-2023.jpg',
            'type' => self::faker()->randomElement(UploadedFileType::cases()),
            'originalFileName' => self::faker()->word().'.'.self::faker()->fileExtension(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(static function (UploadedFile $uploadedFile): void {
                $uploadedFile->setSize(self::faker()->optional()->randomDigit());
                $uploadedFile->setMimeType(self::faker()->optional()->mimeType());
            })
        ;
    }
}
