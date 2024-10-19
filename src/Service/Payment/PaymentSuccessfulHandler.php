<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Payment;
use App\Message\PahekoPaymentSync;
use App\Service\MembershipCreator;
use Doctrine\ORM\EntityManagerInterface;
use Helloasso\Models\Statistics\OrderDetail;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class PaymentSuccessfulHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private MembershipCreator $membershipCreator,
        private MessageBusInterface $bus,
    ) {
    }

    public function onPaymentSuccess(Payment $payment, OrderDetail $order): void
    {
        $payment->approve(\DateTimeImmutable::createFromMutable($order->getDate()));
        $this->em->persist($payment);

        $memberships = $this->membershipCreator->createMembershipsFromPayment($payment);

        foreach ($memberships as $membership) {
            $this->em->persist($membership);
        }

        if (null !== $registration = $payment->getRegistration()) {
            $registration->confirm();

            foreach ($registration->getStagesRegistrations() as $stageRegistration) {
                $stage = $stageRegistration->getStage();
                $stage->updateIsFullProperty();

                $this->em->persist($stage);
            }

            $this->em->persist($registration);
        }

        $this->em->flush();

        $this->bus->dispatch(new PahekoPaymentSync($payment->getId()));
    }
}
