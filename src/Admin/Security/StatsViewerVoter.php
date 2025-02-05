<?php

declare(strict_types=1);

namespace App\Admin\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, mixed>
 */
class StatsViewerVoter extends Voter
{
    #[\Override]
    public function supportsAttribute(string $attribute): bool
    {
        return null !== Permission::tryFrom($attribute);
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

        if (!$user->isStatsViewer()) {
            return false;
        }

        $permission = Permission::from($attribute);

        return \in_array($permission, [
            Permission::ADMIN_ACCESS,

            Permission::EVENT_LIST,
            Permission::EVENT_VIEW_STAGES,
            Permission::EVENT_VIEW_FILLING,

            Permission::STAGE_VIEW,
        ]);
    }
}
