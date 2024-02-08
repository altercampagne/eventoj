<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Payment;
use App\Entity\Registration;
use App\Entity\User;
use App\Form\EventRegistrationPayFormType;
use App\Service\PriceCalculation\RegistrationPriceCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Helloasso\HelloassoClient;
use Helloasso\Models\Carts\CheckoutPayer;
use Helloasso\Models\Carts\InitCheckoutBody;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/registration/{id}/pay', name: 'event_registration_pay')]
class RegistrationPayController extends AbstractController
{
    public function __construct(
        private readonly RegistrationPriceCalculator $registrationPriceCalculator,
        private readonly HelloassoClient $helloassoClient,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request, Registration $registration): Response
    {
        if ($registration->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Current user is not the owner of the given reservation.');
        }
        if (!$registration->isWaitingPayment()) {
            $this->addFlash('warning', 'Cette inscription ne peut pas être réglée. Ne le serait-elle pas déjà ?');

            $this->logger->warning('Trying to pay for a registration which is not in waiting payment!', [
                'registration_id' => (string) $registration->getId(),
            ]);

            return $this->redirectToRoute('homepage');
        }

        $bill = $this->registrationPriceCalculator->calculate($registration);

        $form = $this->createForm(EventRegistrationPayFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $payer */
            $payer = $this->getUser();

            $payment = new Payment(
                payer: $payer,
                amount: $bill->getAmount(),
                registration: $registration,
            );

            $initCheckoutResponse = $this->helloassoClient->checkout->create((new InitCheckoutBody())
                ->setTotalAmount($bill->getAmount())
                ->setInitialAmount($bill->getAmount())
                ->setItemName('Inscription '.$registration->getEvent()->getName())
                ->setBackUrl($this->generateUrl('payment_callback_back', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
                ->setErrorUrl($this->generateUrl('payment_callback_error', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
                ->setReturnUrl($this->generateUrl('payment_callback_return', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
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

            return $this->redirect($initCheckoutResponse->getRedirectUrl());
        }

        return $this->render('event/registration_pay.html.twig', [
            'registration' => $registration,
            'bill' => $bill,
            'form' => $form->createView(),
        ]);
    }
}
