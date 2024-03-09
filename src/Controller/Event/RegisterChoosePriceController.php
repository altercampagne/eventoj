<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\Payment;
use App\Entity\Registration;
use App\Entity\User;
use App\Form\EventRegistration\ChoosePriceFormType;
use App\Form\EventRegistration\EventRegistrationDTO;
use App\Service\MembershipCreator;
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
#[Route('/event/{slug}/register/price', name: 'event_register_choose_price')]
class RegisterChoosePriceController extends AbstractController
{
    public function __construct(
        private readonly MembershipCreator $membershipCreator,
        private readonly HelloassoClient $helloassoClient,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request, Event $event): Response
    {
        if (!$event->isBookable()) {
            throw $this->createNotFoundException();
        }

        /** @var User $user */
        $user = $this->getUser();

        if (null === $registration = $this->em->getRepository(Registration::class)->findOngoingRegistrationForEventAndUser($event, $user)) {
            return $this->redirectToRoute('event_register', ['slug' => $event->getSlug()]);
        }
        if (!$registration->isWaitingPayment()) {
            $this->addFlash('warning', 'Cette inscription ne peut pas être réglée. Ne le serait-elle pas déjà ?');

            $this->logger->warning('Trying to pay for a registration which is not in waiting payment!', [
                'registration_id' => (string) $registration->getId(),
            ]);

            return $this->redirectToRoute('homepage');
        }

        $expectedPrice = $registration->payingDaysOfPresence() * $registration->countPeople() * $registration->getEvent()->getBreakEvenPricePerDay();

        $membershipPricesToPay = $this->membershipCreator->getMembershipPricesToPayForRegistration($registration);
        foreach ($membershipPricesToPay as $membership) {
            $expectedPrice += $membership['price'];
        }

        $eventRegistrationDTO = new EventRegistrationDTO($registration);
        $form = $this->createForm(ChoosePriceFormType::class, $eventRegistrationDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registration->setPricePerDay($eventRegistrationDTO->pricePerDay * 100);
            $this->em->persist($registration);

            /** @var User $payer */
            $payer = $this->getUser();

            $payment = new Payment(
                payer: $payer,
                amount: $expectedPrice,
                registration: $registration,
            );

            $payment->setHelloassoCheckoutIntentId(md5((string) random_int(0, 9999999)));

            $this->em->persist($payment);
            $this->em->flush();

            return $this->redirect($this->generateUrl('payment_callback_return', ['id' => (string) $payment->getId(), 'code' => 'succeeded'], UrlGeneratorInterface::ABSOLUTE_URL));

            $initCheckoutResponse = $this->helloassoClient->checkout->create((new InitCheckoutBody())
                ->setTotalAmount($expectedPrice)
                ->setInitialAmount($expectedPrice)
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

        return $this->render('event/register_choose_price.html.twig', [
            'registration' => $registration,
            'membershipPricesToPay' => $membershipPricesToPay,
            'expectedPrice' => $expectedPrice,
            'form' => $form->createView(),
        ]);
    }
}
