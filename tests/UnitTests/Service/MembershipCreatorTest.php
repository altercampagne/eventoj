<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Service;

use App\Entity\Event;
use App\Entity\Payment;
use App\Entity\Registration;
use App\Entity\Stage;
use App\Entity\StageRegistration;
use App\Entity\User;
use App\Service\MembershipCreator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MembershipCreatorTest extends TestCase
{
    #[DataProvider('createMembershipsFromPaymentProvider')]
    public function testCreateMembershipsFromPayment(string $registrationDate, string $expectedMembershipStartAt): void
    {
        $user = new User();
        $user->setBirthDate(new \DateTimeImmutable('-20 years'));

        $event = Event::AT();
        $stage = new Stage($event);
        $stage->setDate(new \DateTimeImmutable($registrationDate));

        $registration = new Registration($user, $event);
        $registration->setStagesRegistrations([
            new StageRegistration($stage, $registration),
        ]);
        $payment = new Payment($user, 20000, $registration);
        $payment->approve(new \DateTimeImmutable());

        $memberships = (new MembershipCreator())->createMembershipsFromPayment($payment);

        $this->assertCount(1, $memberships);

        $membership = $memberships[0];

        $this->assertSame($expectedMembershipStartAt, $membership->getStartAt()->format('Y-m-d'));
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function createMembershipsFromPaymentProvider(): iterable
    {
        yield ['2024-07-12', '2024-05-01'];
        yield ['2024-02-12', '2023-05-01'];
    }
}
