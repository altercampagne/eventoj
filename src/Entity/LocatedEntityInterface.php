<?php

declare(strict_types=1);

namespace App\Entity;

interface LocatedEntityInterface
{
    public function getAddress(): Address;
}
