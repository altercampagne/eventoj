<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Helloasso\HelloassoClient;
use Helloasso\Models\Carts\CheckoutPayer;
use Helloasso\Models\Carts\InitCheckoutBody;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class RegistrationPaymentHandler
{
    public function __construct(
        private HelloassoClient $helloassoClient,
        private EntityManagerInterface $em,
        private MembershipCreator $membershipCreator,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @return string The redirect URL provided by HelloAsso
     */
    public function initiatePayment(Payment $payment): string
    {
        $registration = $payment->getRegistration();
        $payer = $payment->getPayer();

        $initCheckoutResponse = $this->helloassoClient->checkout->create((new InitCheckoutBody())
            ->setTotalAmount($payment->getAmount())
            ->setInitialAmount($payment->getAmount())
            ->setItemName('Inscription '.$registration->getEvent()->getName())
            ->setBackUrl($this->urlGenerator->generate('payment_callback_back', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setErrorUrl($this->urlGenerator->generate('payment_callback_error', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setReturnUrl($this->urlGenerator->generate('payment_callback_return', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setPayer(
                (new CheckoutPayer())
                    ->setFirstName($payer->getFirstName())
                    ->setLastName($payer->getLastName())
                    ->setEmail($payer->getEmail())
                    ->setAddress($payer->getAddress()->getAddressLine1())
            )
            ->setMetadata([
                'registration' => (string) $registration->getId(),
            ])
        );

        $payment->setHelloassoCheckoutIntentId((string) $initCheckoutResponse->getId());

        $this->em->persist($payment);
        $this->em->flush();

        return $initCheckoutResponse->getRedirectUrl();
    }

    public function approve(Payment $payment): void
    {
        // TODO: Call Helloasso API to ensure the Checkout intent linked to the payment really is approved.

        $payment->approve();
        $payment->getRegistration()->confirm();

        $memberships = $this->membershipCreator->createMembershipsFromRegistration($payment->getRegistration());

        foreach ($memberships as $membership) {
            $this->em->persist($membership);
        }

        $this->em->persist($payment);
        $this->em->persist($payment->getRegistration());
        $this->em->flush();
    }

    public function fail(Payment $payment): void
    {
        $payment->fail();

        $this->em->persist($payment);
        $this->em->flush();
    }
}
