<?php

declare(strict_types=1);

namespace App\Webhook;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\HostRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\IsJsonRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\SchemeRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

final class HelloassoRequestParser extends AbstractRequestParser
{
    public function __construct(
        private readonly LoggerInterface $debugLogger,
    ) {
    }

    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new ChainRequestMatcher([
            new HostRequestMatcher('regex'),
            new MethodRequestMatcher('POST'),
            new IsJsonRequestMatcher(),
            new SchemeRequestMatcher('https'),
        ]);
    }

    /**
     * @throws JsonException
     */
    protected function doParse(Request $request, #[\SensitiveParameter] string $secret): ?RemoteEvent
    {
        $payload = $request->getPayload();

        $this->debugLogger->debug('Received webhook from Helloasso!', [
            'payload' => $payload->all(),
        ]);

        if (!$payload->has('eventType') || !$payload->has('data')) {
            throw new RejectWebhookException(Response::HTTP_BAD_REQUEST, 'Request payload does not contain required fields.');
        }

        $id = $payload->getString('eventType');

        return new RemoteEvent(
            $payload->getString('eventType'),
            $id,
            $payload->all(),
        );
    }
}
