<?php

declare(strict_types=1);

use Symfony\Component\RemoteEvent\RemoteEvent;

/* @phpstan-ignore-next-line */
return new RemoteEvent('payment', 'payment.054fa2a74747481d06670f7d61192bb1', json_decode(file_get_contents(str_replace('.php', '.json', __FILE__)), true, flags: \JSON_THROW_ON_ERROR));
