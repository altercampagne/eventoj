<?php

declare(strict_types=1);

namespace App\Bridge\Helloasso\Webhook;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\IsJsonRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;
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
            new MethodRequestMatcher('POST'),
            new IsJsonRequestMatcher(),
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

        return new RemoteEvent(
            mb_strtolower($payload->getString('eventType')),
            mb_strtolower($payload->getString('eventType')).'.'.md5($request->getContent()),
            $payload->all(),
        );
    }
}
