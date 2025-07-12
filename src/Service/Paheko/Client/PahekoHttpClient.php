<?php

declare(strict_types=1);

namespace App\Service\Paheko\Client;

use App\Service\Paheko\Client\Exception\AdminMemberNotEditableException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class PahekoHttpClient implements PahekoClientInterface
{
    public function __construct(
        private HttpClientInterface $pahekoClient,
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
        $response = $this->pahekoClient->request($method, $uri, $options);

        try {
            return $response->toArray();
        } catch (HttpExceptionInterface|ClientExceptionInterface $e) {
            $error = $e->getResponse()->toArray(false)['error'] ?? null;

            if (403 === $e->getResponse()->getStatusCode() && 'Seul un membre administrateur peut modifier un autre membre administrateur.' === $error) {
                throw new AdminMemberNotEditableException($error, previous: $e);
            }

            throw $e;
        }
    }
}
