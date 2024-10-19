<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Helloasso\HelloassoClient;
use Helloasso\Models\Carts\CheckoutIntentResponse;
use Helloasso\Models\Carts\CheckoutPayer;
use Helloasso\Models\Carts\InitCheckoutBody;
use Helloasso\Models\Statistics\OrderDetail;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class PaymentHandler
{
    public function __construct(
        private HelloassoClient $helloassoClient,
        private EntityManagerInterface $em,
        private UrlGeneratorInterface $urlGenerator,
        private PaymentSuccessfulHandler $paymentSuccessfulHandler,
    ) {
    }

    /**
     * @return string The redirect URL provided by HelloAsso
     */
    public function initiatePayment(Payment $payment): string
    {
        $this->em->persist($payment);

        $returnUrl = $this->urlGenerator->generate('payment_callback_return', ['id' => (string) $payment->getId(), 'code' => 'succeeded'], UrlGeneratorInterface::ABSOLUTE_URL);

        if (0 === $payment->getAmount()) {
            $this->em->flush();

            return $returnUrl;
        }

        $payer = $payment->getPayer();

        $initCheckoutResponse = $this->helloassoClient->checkout->create((new InitCheckoutBody())
            ->setTotalAmount($payment->getAmount())
            ->setInitialAmount($payment->getAmount())
            ->setItemName($this->getHelloassoItemName($payment))
            ->setBackUrl($this->urlGenerator->generate('payment_callback_back', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setErrorUrl($this->urlGenerator->generate('payment_callback_error', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setReturnUrl($returnUrl)
            ->setPayer(
                (new CheckoutPayer())
                    ->setFirstName($payer->getFirstName())
                    ->setLastName($payer->getLastName())
                    ->setEmail($payer->getEmail())
                    ->setAddress($payer->getAddress()->getAddressLine1())
                    ->setCity($payer->getAddress()->getCity())
                    ->setZipCode($payer->getAddress()->getZipCode())
                    ->setDateOfBirth($payer->getBirthDate())
            )
            ->setMetadata([
                'payment' => (string) $payment->getId(),
            ])
        );

        $payment->setHelloassoCheckoutIntentId((string) $initCheckoutResponse->getId());

        $this->em->flush();

        return $initCheckoutResponse->getRedirectUrl();
    }

    public function getCheckoutIntent(Payment $payment): CheckoutIntentResponse
    {
        if (null === $id = $payment->getHelloassoCheckoutIntentId()) {
            throw new \RuntimeException('Given payment does not contains an Helloasso checkout intent ID.');
        }

        return $this->helloassoClient->checkout->retrieve((int) $id);
    }

    public function handlePaymentSuccess(Payment $payment, OrderDetail $order): void
    {
        $this->paymentSuccessfulHandler->onPaymentSuccess($payment, $order);
    }

    public function handlePaymentFailure(Payment $payment): void
    {
        $payment->fail();

        $this->em->persist($payment);
        $this->em->flush();
    }

    private function getHelloassoItemName(Payment $payment): string
    {
        if (null === $registration = $payment->getRegistration()) {
            return 'Adhésion de '.$payment->getPayer()->getFullName();
        }

        return 'Inscription '.$registration->getEvent()->getName();
    }
}
