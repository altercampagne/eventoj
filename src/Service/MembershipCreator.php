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
     * @return array<User|Companion>
     */
    public function getPeopleNeedingMembershipForRegistration(Registration $registration): array
    {
        $memberships = [];

        if (!$this->isMembershipValidForRegistration($registration->getUser()->getLatestMembership(), $registration)) {
            $memberships[] = $registration->getUser();
        }

        foreach ($registration->getCompanions() as $companion) {
            if (!$this->isMembershipValidForRegistration($companion->getLatestMembership(), $registration)) {
                $memberships[] = $companion;
            }
        }

        return $memberships;
    }

    /**
     * @return Membership[]
     */
    public function createMembershipsFromRegistration(Registration $registration): array
    {
        if (null === $payment = $registration->getApprovedPayment()) {
            throw new \LogicException('Cannot create memberships associated to a non paid registration.');
        }

        $membershipStartAt = $this->getMembershipStartAtFromRegistration($registration);

        $memberships = [];

        if (!$this->isMembershipValidForRegistration($registration->getUser()->getLatestMembership(), $registration)) {
            $memberships[] = Membership::createForUser($registration->getUser(), $payment, $membershipStartAt);
        }

        foreach ($registration->getCompanions() as $companion) {
            if (!$this->isMembershipValidForRegistration($companion->getLatestMembership(), $registration)) {
                $memberships[] = Membership::createForCompanion($companion, $payment, $membershipStartAt);
            }
        }

        return $memberships;
    }

    private function isMembershipValidForRegistration(?Membership $membership, Registration $registration): bool
    {
        if (null === $membership) {
            return false;
        }

        if (null === $endAt = $registration->getEndAt()) {
            throw new \LogicException('Given registration does have an end date (does not contains stage registrations?).');
        }

        return $membership->isValidAt($endAt);
    }

    private function getMembershipStartAtFromRegistration(Registration $registration): \DateTimeImmutable
    {
        if (null === $registrationDate = $registration->getEndAt()) {
            throw new \LogicException('Given registration does have an end date (does not contains stage registrations?).');
        }

        $comparisonDate = new \DateTimeImmutable('first day of may');
        $comparisonDate = $comparisonDate->setDate((int) $registrationDate->format('Y'), (int) $comparisonDate->format('m'), (int) $comparisonDate->format('d'));

        if ($registrationDate > $comparisonDate) {
            return $comparisonDate;
        }

        return $comparisonDate->modify('-1 year');
    }
}
