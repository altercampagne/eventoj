<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Alternative;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\SerializerInterface;

class AlternativeFixtures extends AbstractFixture
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $alternatives = $this->serializer->deserialize(file_get_contents(__DIR__.'/alternatives2023.yaml'), Alternative::class.'[]', 'yaml');
        foreach ($alternatives as $alternative) {
            $manager->persist($alternative);
        }

        $manager->flush();

        foreach ($alternatives as $alternative) {
            /* @phpstan-ignore-next-line */
            $this->setReference($alternative->getSlug(), $alternative);
        }
    }
}
