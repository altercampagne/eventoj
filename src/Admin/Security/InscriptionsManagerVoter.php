<?php

declare(strict_types=1);

namespace App\Admin\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, mixed>
 */
class InscriptionsManagerVoter extends Voter
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

        if (!$user->isInscriptionsManager()) {
            return false;
        }

        $permission = Permission::from($attribute);

        return \in_array($permission, [
            Permission::ADMIN_ACCESS,
            Permission::IMAGE_DOWNLOAD_ORIGINAL,

            Permission::EVENT_LIST,
            Permission::EVENT_VIEW_REGISTRATIONS,
            Permission::EVENT_VIEW_STAGES,
            Permission::EVENT_VIEW_FILLING,
            Permission::EVENT_VIEW_MEALS,
            Permission::EVENT_VIEW_ARRIVALS,

            Permission::MEMBERSHIP_LIST,

            Permission::PAYMENT_LIST,
            Permission::PAYMENT_SYNC_WITH_HELLOASSO,
            Permission::PAYMENT_VIEW,

            Permission::QUESTION_LIST,
            Permission::QUESTION_VIEW,
            Permission::QUESTION_CREATE,
            Permission::QUESTION_UPDATE,
            Permission::QUESTION_DELETE,

            Permission::REGISTRATION_LIST,
            Permission::REGISTRATION_VIEW,
            Permission::REGISTRATION_BIKE_ADD,
            Permission::REGISTRATION_BIKE_DELETE,

            Permission::STAGE_VIEW,

            Permission::USER_LIST,
            Permission::USER_VIEW,
        ]);
    }
}
