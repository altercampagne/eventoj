<?php

declare(strict_types=1);

namespace App\Service\Paheko\Client;

use Psr\Log\LoggerInterface;

final class PahekoNullClient implements PahekoClientInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function createUser(array $data): array
    {
        $this->logger->debug(__METHOD__.' have been called.');

        return [
            'id' => random_int(0, 1000000),
        ];
    }

    public function updateUser(string $pahekoId, array $data): array
    {
        $this->logger->debug(__METHOD__.' have been called.');

        return [];
    }

    public function getUsersFromCategory(string $categoryId): array
    {
        $this->logger->debug(__METHOD__.' have been called.');

        return [];
    }

    public function createPayment(array $data): array
    {
        $this->logger->debug(__METHOD__.' have been called.');

        return [
            'id' => random_int(0, 1000000),
        ];
    }

    public function updatePayment(string $pahekoId, array $data): array
    {
        $this->logger->debug(__METHOD__.' have been called.');

        return [];
    }
}
