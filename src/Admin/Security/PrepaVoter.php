<?php

declare(strict_types=1);

namespace App\Admin\Security;

use App\Entity\Stage;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, mixed>
 */
class PrepaVoter extends Voter
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
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (!$user->isPrepa()) {
            return false;
        }

        $permission = Permission::from($attribute);

        if (Permission::STAGE_UPDATE === $permission) {
            if (!$subject instanceof Stage) {
                return false;
            }

            return $user->isPrepaForStage($subject);
        }

        return \in_array($permission, [
            Permission::ADMIN_ACCESS,
            Permission::IMAGE_DOWNLOAD_ORIGINAL,

            Permission::ALTERNATIVE_LIST,
            Permission::ALTERNATIVE_VIEW,
            Permission::ALTERNATIVE_CREATE,
            Permission::ALTERNATIVE_UPDATE,
            Permission::ALTERNATIVE_DELETE,

            Permission::EVENT_LIST,
            Permission::EVENT_VIEW_STAGES,
            Permission::EVENT_VIEW_FILLING,
            Permission::EVENT_VIEW_MEALS,

            Permission::STAGE_VIEW,
        ]);
    }
}
