<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Payment;
use App\Message\PahekoPaymentSync;
use App\Service\Paheko\PaymentSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class PahekoPaymentSyncHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private PaymentSynchronizer $paymentSynchronizer,
    ) {
    }

    public function __invoke(PahekoPaymentSync $message): void
    {
        if (null === $payment = $this->em->getRepository(Payment::class)->find($message->getPaymentId())) {
            throw new \LogicException('Given payment does not exists!');
        }

        $this->paymentSynchronizer->sync($payment);
    }
}
