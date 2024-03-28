<?php

declare(strict_types=1);

namespace App\Admin\Service;

use App\Entity\Companion;
use App\Entity\Event;
use App\Entity\User;

final readonly class SearchResult
{
    /**
     * @param User[]      $users
     * @param Companion[] $companions
     * @param Event[]     $events
     */
    public function __construct(
        public array $users,
        public array $companions,
        public array $events,
    ) {
    }

    public function getNbResults(): int
    {
        return \count($this->users) + \count($this->companions) + \count($this->events);
    }
}
