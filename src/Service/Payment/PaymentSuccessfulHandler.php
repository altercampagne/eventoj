<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Payment;
use App\Message\PahekoRegistrationSync;
use App\Service\MembershipCreator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class PaymentSuccessfulHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private MembershipCreator $membershipCreator,
        private MessageBusInterface $bus,
    ) {
    }

    public function onPaymentSuccess(Payment $payment): void
    {
        // TODO: Call Helloasso API to ensure the Checkout intent linked to the payment really is approved.
        // This must be implemented in the lib first

        $payment->approve();
        $payment->getRegistration()->confirm();

        $memberships = $this->membershipCreator->createMembershipsFromRegistration($payment->getRegistration());

        foreach ($memberships as $membership) {
            $this->em->persist($membership);
        }

        $this->em->persist($payment);
        $this->em->persist($payment->getRegistration());
        $this->em->flush();

        $this->bus->dispatch(new PahekoRegistrationSync($payment->getRegistration()->getId()));
    }
}
