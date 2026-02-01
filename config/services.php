<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Service\Paheko\Client\PahekoClientInterface;
use App\Service\Paheko\Client\PahekoHttpClient;
use App\Service\Paheko\Client\PahekoNullClient;
use App\Service\PriceFormatter;
use Aws\S3\S3Client;
use Helloasso\HelloassoClient;
use Helloasso\HelloassoClientFactory;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('paheko.client.strategy.default', 'http')
        ->set('paheko.client.strategy', '%env(default:paheko.client.strategy.default:PAHEKO_STRATEGY)%')
    ;

    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    // makes classes in src/ available to be used as services
    // this creates a service per class whose id is the fully-qualified class name
    $services->load('App\\', '../src/')
        ->exclude('../src/{DependencyInjection,Entity,Kernel.php}');

    $services->set(HelloassoClient::class)
        ->factory([HelloassoClientFactory::class, 'create'])
        ->arg('$clientId', env('HELLOASSO_CLIENT_ID'))
        ->arg('$clientSecret', env('HELLOASSO_CLIENT_SECRET'))
        ->arg('$organizationSlug', env('HELLOASSO_ORGANISATION_SLUG'))
        ->arg('$httpClient', service('helloasso.client'))
    ;

    $services->set(PriceFormatter::class)
        ->arg('$intlExtension', service('twig.extension.intl'))
    ;

    $services->set(S3Client::class)
        ->arg('$args', [
            'endpoint' => env('S3_ENDPOINT'),
            'region' => env('S3_REGION'),
            'credentials' => [
                'key' => env('S3_KEY'),
                'secret' => env('S3_SECRET'),
            ],
            'use_path_style_endpoint' => env('bool:S3_USE_PATH_STYLE_ENDPOINT'),
            'http' => [
                'verify' => !env('bool:APP_DEBUG'),
            ],
        ])
    ;

    $services->set(PahekoHttpClient::class)->private();
    $services->set(PahekoNullClient::class)->private();

    $services->alias('paheko.client.http', PahekoHttpClient::class)->public();
    $services->alias('paheko.client.null', PahekoNullClient::class)->public();

    $services->set(PahekoClientInterface::class)
        ->factory(expr("parameter('paheko.client.strategy') == 'http' ? service('paheko.client.http') : service('paheko.client.null')"))
    ;
};
