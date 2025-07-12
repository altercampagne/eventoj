<?php

declare(strict_types=1);

namespace App\Factory\Document;

use App\Entity\Document\EventPicture;
use App\Factory\EventFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<EventPicture>
 */
final class EventPictureFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return EventPicture::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'event' => EventFactory::new(),
            'originalFileName' => self::faker()->text(),
            'path' => self::faker()->text(),
            'user' => UserFactory::new(),
        ];
    }
}
