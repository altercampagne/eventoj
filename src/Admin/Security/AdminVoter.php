<?php

declare(strict_types=1);

namespace App\Admin\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminVoter extends Voter
{
    /* @phpstan-ignore-next-line */
    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return null !== Permission::tryFrom($attribute);
    }

    #[\Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $user->isAdmin();
    }
}
