<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Helloasso\HelloassoClient;
use Helloasso\Models\Carts\CheckoutPayer;
use Helloasso\Models\Carts\InitCheckoutBody;
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
        $payer = $payment->getPayer();

        $initCheckoutResponse = $this->helloassoClient->checkout->create((new InitCheckoutBody())
            ->setTotalAmount($payment->getAmount())
            ->setInitialAmount($payment->getAmount())
            ->setItemName($this->getHelloassoItemName($payment))
            ->setBackUrl($this->urlGenerator->generate('payment_callback_back', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setErrorUrl($this->urlGenerator->generate('payment_callback_error', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setReturnUrl($this->urlGenerator->generate('payment_callback_return', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
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

        $this->em->persist($payment);
        $this->em->flush();

        return $initCheckoutResponse->getRedirectUrl();
    }

    public function isPaymentSuccessful(Payment $payment): bool
    {
        $checkoutIntent = $this->helloassoClient->checkout->retrieve((int) $payment->getHelloassoCheckoutIntentId());

        return null !== $checkoutIntent->getOrder();
    }

    public function handlePaymentSuccess(Payment $payment): void
    {
        $this->paymentSuccessfulHandler->onPaymentSuccess($payment);
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
