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
final readonly class HelloassoWebhookConsumer implements ConsumerInterface
{
    public function __construct(
        private HelloassoClient $helloassoClient,
        private EntityManagerInterface $em,
        private PaymentRefundHandler $paymentRefundHandler,
        private LoggerInterface $logger,
        #[Autowire(env: 'bool:STAGING')]
        private bool $staging,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        $eventAsString = json_encode($event->getPayload(), \JSON_THROW_ON_ERROR);
        $helloassoEvent = $this->helloassoClient->decodeEvent($eventAsString);

        if (Event::EVENT_TYPE_PAYMENT !== $helloassoEvent->getEventType()) {
            $this->logger->info("Given event type is \"{$helloassoEvent->getEventType()}\".");

            return;
        }

        /** @var PaymentDetail $helloassoPayment */
        $helloassoPayment = $helloassoEvent->getData();

        $state = $helloassoPayment->getState();
        if (PaymentState::Refunding !== $state && PaymentState::Refunded !== $state) {
            $this->logger->info("Payment {$helloassoPayment->getId()} has state \"{$helloassoPayment->getState()->value}\". Only refunded payments are handled by the webhook consumer.");

            return;
        }

        if (null === $payment = $this->em->getRepository(Payment::class)->findOneByHelloassoOrderId($helloassoPayment->getOrder()->getId())) {
            // In staging, we receive webhooks when a payment is made on a local environment.
            if ($this->staging) {
                return;
            }

            throw new \RuntimeException("Unable to find a payment with helloasso Order ID \"{$helloassoPayment->getOrder()->getId()}\"");
        }

        // This is the expected behaviour (as payments are refunded by the admin).
        if ($payment->isRefunded()) {
            return;
        }

        $this->logger->warning('A payment have been refunded from the helloasso admin! Doing the refund in the app too.', [
            'payment' => $payment,
        ]);

        $this->paymentRefundHandler->refund($payment);
    }
}
