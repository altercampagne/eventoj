<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Service\Payment;

use App\Entity\Event;
use App\Entity\Payment;
use App\Entity\Registration;
use App\Entity\User;
use App\Service\Paheko\PaymentSynchronizer;
use App\Service\Payment\PaymentRefundHandler;
use Doctrine\ORM\EntityManagerInterface;
use Helloasso\HelloassoClient;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class PaymentRefundHandlerTest extends TestCase
{
    public function testRefundCancelsRegistration(): void
    {
        $payment = $this->createApprovedPayment();
        $registration = $payment->getRegistration();
        $this->assertNotNull($registration);

        $this->createHandler()->refund($payment);

        $this->assertTrue($payment->isRefunded());
        $this->assertTrue($registration->isCanceled());
    }

    public function testRefundDoesNotFailWhenRegistrationIsAlreadyCanceled(): void
    {
        $payment = $this->createApprovedPayment();
        $registration = $payment->getRegistration();
        $this->assertNotNull($registration);

        $registration->cancel();
        $canceledAt = $registration->getCanceledAt();

        $this->createHandler()->refund($payment);

        $this->assertTrue($payment->isRefunded());
        $this->assertTrue($registration->isCanceled());
        $this->assertSame($canceledAt, $registration->getCanceledAt());
    }

    private function createApprovedPayment(): Payment
    {
        $user = new User();
        $user->setBirthDate(new \DateTimeImmutable('-20 years'));

        // An amount of 0 prevents the Paheko synchronization (no call is made).
        $payment = new Payment($user, 0, new Registration($user, Event::AT()));
        $payment->approve('45345698', new \DateTimeImmutable());

        return $payment;
    }

    private function createHandler(): PaymentRefundHandler
    {
        return new PaymentRefundHandler(
            $this->createStub(EntityManagerInterface::class),
            new \ReflectionClass(HelloassoClient::class)->newInstanceWithoutConstructor(),
            new \ReflectionClass(PaymentSynchronizer::class)->newInstanceWithoutConstructor(),
            new NullLogger(),
            'test',
        );
    }
}
