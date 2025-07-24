<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Email\RegistrationConfirmationSender;
use App\Entity\Payment;
use App\Message\PahekoPaymentSync;
use App\Service\MembershipCreator;
use Doctrine\ORM\EntityManagerInterface;
use Helloasso\Models\Statistics\OrderDetail;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class PaymentSuccessfulHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private MembershipCreator $membershipCreator,
        private MessageBusInterface $bus,
        private RegistrationConfirmationSender $registrationConfirmationSender,
        private LoggerInterface $logger,
    ) {
    }

    public function onPaymentSuccess(Payment $payment, OrderDetail $order): void
    {
        if ($payment->isApproved()) {
            $this->logger->notice('Given payment is already approved, nothing to do! 👌', [
                'payment' => $payment,
            ]);

            return;
        }

        $payment->approve((string) $order->getId(), \DateTimeImmutable::createFromMutable($order->getDate()));
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

            $this->registrationConfirmationSender->send($registration);
        }

        $this->em->flush();

        $this->bus->dispatch(new PahekoPaymentSync($payment->getId()));
    }
}
