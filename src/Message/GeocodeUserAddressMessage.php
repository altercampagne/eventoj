<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Uid\Uuid;

final readonly class GeocodeUserAddressMessage
{
    public function __construct(
        private Uuid $id,
    ) {
    }

    public function getUserId(): Uuid
    {
        return $this->id;
    }
}
