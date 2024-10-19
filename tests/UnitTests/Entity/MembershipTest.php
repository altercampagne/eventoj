<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Entity;

use App\Entity\Event;
use App\Entity\Membership;
use App\Entity\Payment;
use App\Entity\Registration;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class MembershipTest extends TestCase
{
    public function testIsValidAt(): void
    {
        $user = new User();
        $user->setBirthDate(new \DateTimeImmutable('-20 years'));

        $payment = new Payment($user, 20000, new Registration($user, Event::AT()));
        $payment->approve(new \DateTimeImmutable());

        $membership = Membership::createForUser($user, $payment, new \DateTimeImmutable('2024-05-01'));

        $this->assertFalse($membership->isValidAt(new \DateTimeImmutable('2024-04-30')));

        $this->assertTrue($membership->isValidAt(new \DateTimeImmutable('2024-05-01')));
        $this->assertTrue($membership->isValidAt(new \DateTimeImmutable('2024-12-12')));

        $this->assertFalse($membership->isValidAt(new \DateTimeImmutable('2025-05-01')));
    }
}
