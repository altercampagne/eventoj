<?php

declare(strict_types=1);

namespace App\Service\Paheko;

use App\Entity\User;
use App\Service\Paheko\Client\Exception\AdminMemberNotEditableException;
use App\Service\Paheko\Client\PahekoClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ClientException;

/**
 * @see https://paheko.cloud/api#membres
 */
final readonly class UserSynchronizer
{
    public function __construct(
        private PahekoClientInterface $pahekoClient,
        private EntityManagerInterface $em,
        private PhoneNumberHelper $phoneNumberHelper,
        private LoggerInterface $logger,
    ) {
    }

    public function sync(User $user): void
    {
        if (null !== $user->getPahekoId()) {
            $this->updateUser($user);

            return;
        }

        try {
            $pahekoUser = $this->pahekoClient->createUser(array_merge(['id_category' => 1], $this->getUserData($user)));
            /* @phpstan-ignore-next-line */
            $id = (string) $pahekoUser['id'];
        } catch (ClientException $e) {
            // Paheko can return a 409 when the current user is perfectly the
            // same (same email & fullname) or a 400 when the email is already
            // used.
            if (null === $id = $this->findExistingUserId($user)) {
                $this->logger->error('Conflict when creating user but no user found with the same email.', [
                    'exception' => $e,
                    'user' => $user,
                ]);

                return;
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

        try {
            $this->pahekoClient->updateUser($pahekoId, $this->getUserData($user));
        } catch (AdminMemberNotEditableException) {
            // It looks like event with a token with admin privileges it's
            // not possible to modify a member which have access to the
            // configuration.
            // As it's not possible to do anyhting against this, we
            // silently ignore this "error".
        }
    }

    private function findExistingUserId(User $user): ?string
    {
        $categories = $this->pahekoClient->getUserCategories();
        foreach ($categories as $category) {
            $pahekoUsers = $this->pahekoClient->getUsersFromCategory((string) $category['id']);
            $nbPahekoUsers = \count($pahekoUsers);

            $this->logger->debug("Searching for {$user->getEmail()} in {$nbPahekoUsers} users from category {$category['name']}...");

            foreach ($pahekoUsers as $pahekoUser) {
                if (mb_strtolower((string) $pahekoUser['email']) === mb_strtolower($user->getEmail())) {
                    $this->logger->debug("Matchin user found (Paheko ID is {$pahekoUser['numero']})");

                    return (string) $pahekoUser['numero'];
                }

                $this->logger->debug("Paheko email \"{$pahekoUser['email']} is not matching {$user->getEmail()}");
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
            $addressLines .= "\n{$line2}";
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
