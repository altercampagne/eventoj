<?php

declare(strict_types=1);

namespace App\Bridge\Geocoder\ProviderFactory;

use Bazinga\GeocoderBundle\ProviderFactory\AbstractFactory;
use Geocoder\Provider\Photon\Photon;
use Geocoder\Provider\Provider;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PhotonProviderFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => Photon::class, 'packageName' => 'geocoder-php/photon-provider'],
    ];

    protected function getProvider(array $config): Provider
    {
        $httpClient = $config['http_client'] ?? $this->httpClient;

        /* @phpstan-ignore-next-line */
        return Photon::withKomootServer($httpClient);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'http_client' => null,
        ]);

        $resolver->setAllowedTypes('http_client', ['object', 'null']);
    }
}
