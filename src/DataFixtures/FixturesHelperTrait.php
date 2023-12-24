<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;

trait FixturesHelperTrait
{
    public function getRandomUser(): User
    {
        $reference = array_rand($this->getReferenceRepository()->getReferencesByClass()[User::class]);

        return $this->getReference($reference, User::class);
    }

    private function getReferenceRepository(): ReferenceRepository
    {
        \assert(null !== $this->referenceRepository);

        return $this->referenceRepository;
    }
}
