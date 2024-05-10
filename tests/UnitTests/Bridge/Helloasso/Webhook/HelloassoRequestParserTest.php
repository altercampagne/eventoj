<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Bridge\Helloasso\Webhook;

use App\Bridge\Helloasso\Webhook\HelloassoRequestParser;
use Psr\Log\NullLogger;
use Symfony\Component\Webhook\Client\RequestParserInterface;
use Symfony\Component\Webhook\Test\AbstractRequestParserTestCase;

final class HelloassoRequestParserTest extends AbstractRequestParserTestCase
{
    protected function createRequestParser(): RequestParserInterface
    {
        return new HelloassoRequestParser(new NullLogger());
    }
}
