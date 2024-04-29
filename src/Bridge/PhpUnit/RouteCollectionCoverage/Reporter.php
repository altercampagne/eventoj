<?php

declare(strict_types=1);

namespace App\Bridge\PhpUnit\RouteCollectionCoverage;

use Symfony\Component\Routing\RouteCollection;

/**
 * @internal
 */
final class Reporter
{
    public function __construct(
        private readonly int $maximumRoutesToDisplay = 25,
    ) {
    }

    public function report(RouteCollection $routeCollection): ?string
    {
        $routeNames = $this->extractRelevantRouteNamesFromRouteCollection($routeCollection);

        if (0 === $count = \count($routeNames)) {
            return null;
        }

        $items = implode("\n- ", \array_slice($routeNames, 0, $this->maximumRoutesToDisplay, true));

        if ($count > $this->maximumRoutesToDisplay) {
            $remainingCount = $count - $this->maximumRoutesToDisplay;

            return <<<TXT


                Found {$count} route(s) which have not been tested!

                - {$items}

                There are {$remainingCount} additional non tested routes that are not listed here.
                TXT;
        }

        return <<<TXT


            Found {$count} route(s) which have not been tested!

            - {$items}
            TXT;
    }

    /** @return string[] */
    private function extractRelevantRouteNamesFromRouteCollection(RouteCollection $routeCollection): array
    {
        return array_keys($routeCollection->all());
    }
}
