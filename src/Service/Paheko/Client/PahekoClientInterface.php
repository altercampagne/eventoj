<?php

declare(strict_types=1);

namespace App\Service\Paheko\Client;

interface PahekoClientInterface
{
    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function createUser(array $data): array;

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function updateUser(string $pahekoId, array $data): array;

    /**
     * @return array<int, array<string, int|string|null>>
     */
    public function getUsersFromCategory(string $category): array;

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function createPayment(array $data): array;

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function updatePayment(string $pahekoId, array $data): array;
}
