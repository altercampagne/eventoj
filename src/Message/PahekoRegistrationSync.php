<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Uid\UuidV4;

final class PahekoRegistrationSync
{
    public function __construct(
        private readonly UuidV4 $id,
    ) {
    }

    public function getRegistrationId(): UuidV4
    {
        return $this->id;
    }
}
