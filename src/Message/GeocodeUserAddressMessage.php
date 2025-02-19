<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Uid\UuidV4;

final readonly class GeocodeUserAddressMessage
{
    public function __construct(
        private UuidV4 $id,
    ) {
    }

    public function getUserId(): UuidV4
    {
        return $this->id;
    }
}
