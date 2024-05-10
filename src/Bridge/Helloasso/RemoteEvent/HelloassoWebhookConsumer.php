<?php

declare(strict_types=1);

namespace App\Bridge\Helloasso\RemoteEvent;

use App\Entity\Payment;
use App\Service\Payment\PaymentRefundHandler;
use Doctrine\ORM\EntityManagerInterface;
use Helloasso\Enums\PaymentState;
use Helloasso\Models\Statistics\Payment as HelloassoPayment;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[AsRemoteEventConsumer('helloasso')]
final class HelloassoWebhookConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly DenormalizerInterface $serializer,
        private readonly EntityManagerInterface $em,
        private readonly PaymentRefundHandler $paymentRefundHandler,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        if ('payment' !== $event->getName()) {
            return;
        }

        $helloassoPayment = $this->serializer->denormalize($event->getPayload(), HelloassoPayment::class);
        $state = $helloassoPayment->getState();

        if (PaymentState::Refunding !== $state && PaymentState::Refunded === $state) {
            return;
        }

        if (null === $payment = $this->em->getRepository(Payment::class)->findOneByHelloassoCheckoutIntentId($helloassoPayment->getId())) {
            throw new \RuntimeException("Unable to find a payment with helloasso ID \"{$helloassoPayment->getId()}\"");
        }

        $this->paymentRefundHandler->fullRefund($payment);
    }
}
