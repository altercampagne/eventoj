<?php

declare(strict_types=1);

namespace App\Bridge\Helloasso\RemoteEvent;

use App\Entity\Payment;
use App\Service\Payment\PaymentRefundHandler;
use Doctrine\ORM\EntityManagerInterface;
use Helloasso\Enums\PaymentState;
use Helloasso\HelloassoClient;
use Helloasso\Models\Event;
use Helloasso\Models\Statistics\PaymentDetail;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('helloasso')]
final class HelloassoWebhookConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly HelloassoClient $helloassoClient,
        private readonly EntityManagerInterface $em,
        private readonly PaymentRefundHandler $paymentRefundHandler,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        $eventAsString = json_encode($event->getPayload(), \JSON_THROW_ON_ERROR);
        $event = $this->helloassoClient->decodeEvent($eventAsString);

        if (Event::EVENT_TYPE_PAYMENT !== $event->getEventType()) {
            return;
        }

        /** @var PaymentDetail $helloassoPayment */
        $helloassoPayment = $event->getData();

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
