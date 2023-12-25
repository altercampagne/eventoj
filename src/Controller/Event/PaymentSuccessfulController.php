<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Registration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class PaymentSuccessfulController extends AbstractController
{
    #[Route('/registration/{id}/payment_successful', name: 'event_registration_payment_successful')]
    public function __invoke(Request $request, Registration $registration): Response
    {
        if ($registration->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Current user is not the owner of the given reservation.');
        }

        if (!$registration->isConfirmed()) {
            return $this->redirectToRoute('event_register', [
                'id' => $registration->getEvent()->getId(),
                'registration' => $registration->getId(),
            ]);
        }

        return $this->render('event/payment_successful.html.twig', [
            'registration' => $registration,
        ]);
    }
}
