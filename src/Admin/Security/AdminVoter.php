<?php

declare(strict_types=1);

namespace App\Admin\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, mixed>
 */
class AdminVoter extends Voter
{
    #[\Override]
    public function supportsAttribute(string $attribute): bool
    {
        return null !== Permission::tryFrom($attribute) && Permission::DEBUG !== Permission::from($attribute);
    }

    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $this->supportsAttribute($attribute);
    }

    #[\Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $permission = Permission::from($attribute);

        if (Permission::USER_MANAGEMENT === $permission && $token->getUser() === $subject) {
            return false;
        }

        return $user->isAdmin();
    }
}
