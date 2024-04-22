<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Alternative;
use App\Message\GeocodeAlternativeAddressMessage;
use App\Service\AddressGeocoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GeocodeAlternativeAddressMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AddressGeocoder $addressGeocoder,
    ) {
    }

    public function __invoke(GeocodeAlternativeAddressMessage $message): void
    {
        if (null === $alternative = $this->em->getRepository(Alternative::class)->findOneById($message->getAlternativeId())) {
            throw new \InvalidArgumentException('Given alternative not found!');
        }

        $this->addressGeocoder->geocode($alternative);
    }
}
