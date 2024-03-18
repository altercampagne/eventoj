<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

final class Station
{
    public const TYPE_TRAIN = 'train';
    public const TYPE_BUS = 'bus';

    public function __construct(
        #[Assert\NotNull]
        #[Assert\Choice([self::TYPE_TRAIN, self::TYPE_BUS])]
        public string $type,

        #[Assert\NotNull]
        public string $name,

        #[Assert\NotNull]
        #[Assert\GreaterThanOrEqual(value: 1)]
        public int $distance,
    ) {
    }
}
