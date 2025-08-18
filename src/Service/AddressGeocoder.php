<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Address;
use App\Entity\LocatedEntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Geocoder\Collection;
use Geocoder\Model\Coordinates;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class AddressGeocoder
{
    public function __construct(
        private Provider $photonGeocoder,
        private EntityManagerInterface $em,
        #[Autowire(param: 'kernel.environment')]
        private string $environment,
    ) {
    }

    public function geocode(LocatedEntityInterface $entity): bool
    {
        if ('test' === $this->environment) {
            return true;
        }

        $address = $entity->getAddress();

        $query = GeocodeQuery::create((string) $address)->withLocale('fr');

        $results = $this->photonGeocoder->geocodeQuery($query);

        if ($results->isEmpty()) {
            return false;
        }

        if (null === $coordinates = $this->extractCoordinatesForAddress($address, $results)) {
            return false;
        }

        $address->setLatitude($coordinates->getLatitude());
        $address->setLongitude($coordinates->getLongitude());

        $this->em->persist($entity);
        $this->em->flush();

        return true;
    }

    private function extractCoordinatesForAddress(Address $address, Collection $results): ?Coordinates
    {
        foreach ($results->all() as $location) {
            if ($location->getPostalCode() !== $address->getZipCode()) {
                continue;
            }

            if (null !== $coordinates = $location->getCoordinates()) {
                return $coordinates;
            }
        }

        return null;
    }
}
