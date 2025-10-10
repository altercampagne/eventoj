<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Uid\Uuid;

final readonly class GeocodeAlternativeAddressMessage
{
    public function __construct(
        private Uuid $id,
    ) {
    }

    public function getAlternativeId(): Uuid
    {
        return $this->id;
    }
}
