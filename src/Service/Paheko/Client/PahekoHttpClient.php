<?php

declare(strict_types=1);

namespace App\Service\Paheko\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PahekoHttpClient implements PahekoClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $pahekoClient,
    ) {
    }

    public function createUser(array $data): array
    {
        return $this->pahekoClient->request('POST', 'user/new', [
            'body' => $data,
        ])->toArray();
    }

    public function updateUser(string $pahekoId, array $data): array
    {
        return $this->pahekoClient->request('POST', "user/$pahekoId", [
            'body' => $data,
        ])->toArray();
    }

    public function getUserCategories(): array
    {
        return $this->pahekoClient->request('GET', 'user/categories')->toArray();
    }

    public function getUsersFromCategory(string $categoryId): array
    {
        return $this->pahekoClient->request('POST', "user/category/$categoryId.json")->toArray();
    }

    public function createPayment(array $data): array
    {
        return $this->pahekoClient->request('POST', 'accounting/transaction', [
            'body' => $data,
        ])->toArray();
    }

    public function updatePayment(string $pahekoId, array $data): array
    {
        return $this->pahekoClient->request('POST', "accounting/transaction/$pahekoId", [
            'body' => $data,
        ])->toArray();
    }
}
