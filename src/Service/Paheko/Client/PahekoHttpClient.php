<?php

declare(strict_types=1);

namespace App\Service\Paheko\Client;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class PahekoHttpClient implements PahekoClientInterface
{
    public function __construct(
        private HttpClientInterface $pahekoClient,
        private LoggerInterface $logger,
    ) {
    }

    public function createUser(array $data): array
    {
        /* @phpstan-ignore return.type */
        return $this->request('POST', 'user/new', [
            'body' => $data,
        ]);
    }

    public function updateUser(string $pahekoId, array $data): array
    {
        /* @phpstan-ignore return.type */
        return $this->request('POST', "user/{$pahekoId}", [
            'body' => $data,
        ]);
    }

    public function getUserCategories(): array
    {
        /* @phpstan-ignore-next-line */
        return $this->request('GET', 'user/categories');
    }

    public function getUsersFromCategory(string $categoryId): array
    {
        /* @phpstan-ignore-next-line */
        return $this->request('POST', "user/category/{$categoryId}.json");
    }

    public function createPayment(array $data): array
    {
        /* @phpstan-ignore return.type */
        return $this->request('POST', 'accounting/transaction', [
            'body' => $data,
        ]);
    }

    public function updatePayment(string $pahekoId, array $data): array
    {
        /* @phpstan-ignore return.type */
        return $this->request('POST', "accounting/transaction/{$pahekoId}", [
            'body' => $data,
        ]);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<mixed>
     */
    private function request(string $method, string $uri, array $options = []): array
    {
        try {
            return $this->pahekoClient->request($method, $uri, $options)->toArray();
        } catch (HttpExceptionInterface $e) {
            $this->logger->error('Error when calling Paheko API.', [
                'request_method' => $method,
                'request_uri' => $uri,
                'response_code' => $e->getResponse()->getStatusCode(),
                'response_content' => $e->getResponse()->getContent(false),
                'options' => $options,
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}
