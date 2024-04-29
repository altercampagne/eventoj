<?php

declare(strict_types=1);

namespace App\Bridge\PhpUnit\RouteCollectionCoverage;

use PHPUnit\Runner\AfterLastTestHook;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;

final class Extension extends KernelTestCase implements AfterLastTestHook
{
    public function executeAfterLastTest(): void
    {
        /** @var ?RouterInterface $router */
        $router = static::getContainer()->get(RouterInterface::class);
        if (null === $router) {
            static::ensureKernelShutdown();

            return;
        }

        $routeCollection = $router->getRouteCollection();
        static::ensureKernelShutdown();

        if (0 === $routeCollection->count()) {
            return;
        }

        if (null === $report = (new Reporter())->report($routeCollection)) {
            return;
        }

        echo $report;
    }
}
