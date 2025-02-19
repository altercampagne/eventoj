<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Uid\UuidV4;

final readonly class GeocodeAlternativeAddressMessage
{
    public function __construct(
        private UuidV4 $id,
    ) {
    }

    public function getAlternativeId(): UuidV4
    {
        return $this->id;
    }
}
