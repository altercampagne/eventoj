<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Registration;
use App\Entity\User;
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
class PayController extends AbstractController
{
    public function __construct(
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

            $this->logger->warning('Trying to pay a registration which is not in waiting payment!', [
                'registration_id' => (string) $registration->getId(),
            ]);

            return $this->redirectToRoute('homepage');
        }

        /** @var User $payer */
        $payer = $this->getUser();

        /* @phpstan-ignore-next-line */
        $response = $this->helloassoClient->checkout->create((new InitCheckoutBody())
            ->setTotalAmount($registration->getTotalPrice())
            ->setInitialAmount($registration->getTotalPrice())
            ->setItemName('Inscription '.$registration->getEvent()->getName())
            ->setBackUrl($this->generateUrl('event_registration_post_payment_back', ['id' => (string) $registration->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setErrorUrl($this->generateUrl('event_registration_post_payment_error', ['id' => (string) $registration->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setReturnUrl($this->generateUrl('event_registration_post_payment_return', ['id' => (string) $registration->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
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

        $registration->setHelloassoCheckoutIntentId((string) $response->getId());

        $this->em->persist($registration);
        $this->em->flush();

        return $this->redirect($response->getRedirectUrl());
    }
}
