<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\GeocodeUserAddressMessage;
use App\Service\AddressGeocoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GeocodeUserAddressMessageHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private AddressGeocoder $addressGeocoder,
    ) {
    }

    public function __invoke(GeocodeUserAddressMessage $message): void
    {
        if (null === $user = $this->em->getRepository(User::class)->findOneById($message->getUserId())) {
            throw new \InvalidArgumentException('Given user not found!');
        }

        $this->addressGeocoder->geocode($user);
    }
}
