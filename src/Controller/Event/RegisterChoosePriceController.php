<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\Payment;
use App\Entity\Registration;
use App\Entity\User;
use App\Form\EventRegistration\ChoosePriceFormType;
use App\Service\Bill\BillCreator;
use App\Service\Payment\PaymentHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/event/{slug}/register/price', name: 'event_register_choose_price')]
class RegisterChoosePriceController extends AbstractController
{
    public function __construct(
        private readonly PaymentHandler $paymentHandler,
        private readonly BillCreator $billCreator,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Event $event,
    ): Response {
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

        $bill = $this->billCreator->create($registration);

        $form = $this->createForm(ChoosePriceFormType::class, ['price' => $bill->getBreakEvenPrice()], [
            'minimum_price' => $bill->getMinimumPrice(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $price = (int) $form->getData()['price'];

            $registration->setPrice($price);
            $this->em->persist($registration);
            $this->em->flush();

            /** @var User $payer */
            $payer = $this->getUser();

            $payment = new Payment(
                payer: $payer,
                amount: $price,
                registration: $registration,
            );

            $redirectUrl = $this->paymentHandler->initiatePayment($payment);

            return $this->redirect($redirectUrl);
        }

        return $this->render('event/register_choose_price.html.twig', [
            'registration' => $registration,
            'bill' => $bill,
            'form' => $form->createView(),
        ]);
    }
}
