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
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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
        private readonly LoggerInterface $debugLogger,
        #[Autowire(env: 'bool:STAGING')]
        private bool $staging,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        $eventAsString = json_encode($event->getPayload(), \JSON_THROW_ON_ERROR);
        $helloassoEvent = $this->helloassoClient->decodeEvent($eventAsString);

        if (Event::EVENT_TYPE_PAYMENT !== $helloassoEvent->getEventType()) {
            $this->debugLogger->info("Given event type is \"{$helloassoEvent->getEventType()}\".");

            return;
        }

        /** @var PaymentDetail $helloassoPayment */
        $helloassoPayment = $helloassoEvent->getData();

        $state = $helloassoPayment->getState();
        if (PaymentState::Refunding !== $state && PaymentState::Refunded === $state) {
            $stateAsString = $helloassoPayment->getState()->value;
            $this->debugLogger->info("Payment {$helloassoPayment->getId()} has state \"{$stateAsString}\".");

            return;
        }

        if (null === $payment = $this->em->getRepository(Payment::class)->findOneByHelloassoCheckoutIntentId($helloassoPayment->getId())) {
            // In stagign, we receive webhooks when a payment is made on a local environment.
            if ($this->staging) {
                return;
            }

            throw new \RuntimeException("Unable to find a payment with helloasso ID \"{$helloassoPayment->getId()}\"");
        }

        $this->paymentRefundHandler->fullRefund($payment);
    }
}
