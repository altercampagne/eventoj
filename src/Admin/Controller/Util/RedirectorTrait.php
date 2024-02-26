<?php

declare(strict_types=1);

namespace App\Admin\Controller\Util;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

trait RedirectorTrait
{
    /**
     * @param array<string, mixed> $parameters
     */
    protected function redirectToRefererOrToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        /** @var RequestStack $requestStack */
        $requestStack = $this->container->get('request_stack');
        if (null === $request = $requestStack->getCurrentRequest()) {
            throw new \LogicException('RedirectorTrait must be used in a HTTP context.');
        }

        if (null !== $targetUrl = $request->headers->get('Referer')) {
            return $this->redirect($targetUrl);
        }

        return $this->redirectToRoute($route, $parameters, $status);
    }
}
