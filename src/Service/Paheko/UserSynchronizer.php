<?php

declare(strict_types=1);

namespace App\Service\Paheko;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @see https://paheko.cloud/api#membres
 */
final class UserSynchronizer
{
    public function __construct(
        private readonly HttpClientInterface $pahekoClient,
        private readonly EntityManagerInterface $em,
        private readonly PhoneNumberHelper $phoneNumberHelper,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function sync(User $user): void
    {
        if (null !== $user->getPahekoId()) {
            $this->updateUser($user);

            return;
        }

        $response = $this->pahekoClient->request('POST', 'user/new', [
            'body' => array_merge([
                'id_category' => 1,
            ], $this->getUserData($user)),
        ]);

        try {
            $id = (string) $response->toArray()['id'];
        } catch (ClientException $e) {
            if (409 !== $e->getResponse()->getStatusCode()) {
                throw $e;
            }

            // We have a duplicate user! Let's retrieve the existing paheko user.
            if (null === $id = $this->findExistingUserId($user)) {
                $this->logger->error('Unable to find duplicate user!', [
                    'user' => $user,
                ]);

                throw new \LogicException('We have a duplicate user which does not seems to match current user!', 0, $e);
            }
        }

        $user->setPahekoId($id);

        $this->em->persist($user);
        $this->em->flush();
    }

    private function updateUser(User $user): void
    {
        if (null === $pahekoId = $user->getPahekoId()) {
            throw new \LogicException('Given user does not have a paheko ID!');
        }

        $this->pahekoClient->request('POST', "user/$pahekoId", [
            'body' => $this->getUserData($user),
        ])->toArray();
    }

    private function findExistingUserId(User $user): ?string
    {
        $response = $this->pahekoClient->request('POST', 'user/category/1.json');
        $pahekoUsers = $response->toArray();

        foreach ($pahekoUsers as $pahekoUser) {
            if ($pahekoUser['email'] === $user->getEmail() && $pahekoUser['nom'] === $user->getFullName()) {
                return (string) $pahekoUser['numero'];
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function getUserData(User $user): array
    {
        $address = $user->getAddress();

        $addressLines = $address->getAddressLine1();
        if (null !== $line2 = $address->getAddressLine2()) {
            $addressLines .= "\n$line2";
        }

        return [
            'nom' => $user->getFullname(),
            'email' => $user->getEmail(),
            'adresse' => $addressLines,
            'code_postal' => $address->getZipCode(),
            'ville' => $address->getCity(),
            'telephone' => $this->phoneNumberHelper->format($user->getPhoneNumber()),
            // 'date_inscription' => '',
        ];
    }
}
