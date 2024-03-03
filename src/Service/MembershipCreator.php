<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Companion;
use App\Entity\Membership;
use App\Entity\Registration;
use App\Entity\User;

final class MembershipCreator
{
    /**
     * Return an array containing all users which need a membership (as key)
     * and the price they need to pay (as value).
     *
     * @return array<array{person: User|Companion, price: int}>
     */
    public function getMembershipPricesToPayForRegistration(Registration $registration): array
    {
        $memberships = [];

        if (!$this->isMembershipValidForRegistration($registration->getUser()->getLatestMembership(), $registration)) {
            $memberships[] = [
                'person' => $registration->getUser(),
                'price' => $this->getPriceForPerson($registration->getUser()),
            ];
        }

        foreach ($registration->getCompanions() as $companion) {
            if (!$this->isMembershipValidForRegistration($companion->getLatestMembership(), $registration)) {
                $memberships[] = [
                    'person' => $companion,
                    'price' => $this->getPriceForPerson($companion),
                ];
            }
        }

        return $memberships;
    }

    public static function getPriceForPerson(User|Companion $person): int
    {
        if (18 <= $person->getAge()) {
            return 2000;
        }
        if (3 <= $person->getAge()) {
            return 1000;
        }

        return 0;
    }

    private function isMembershipValidForRegistration(?Membership $membership, Registration $registration): bool
    {
        if (null === $membership) {
            return false;
        }

        if (null === $stageRegistration = $registration->getLastStageRegistration()) {
            throw new \LogicException('Given registration does not contains any stage!');
        }

        return $membership->isValidAt($stageRegistration->getStage()->getDate());
    }
}
