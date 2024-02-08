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
#[Route('/registration/{id}/confirmed', name: 'event_registration_confirmed')]
class RegistrationConfirmedController extends AbstractController
{
    public function __invoke(Request $request, Registration $registration): Response
    {
        if ($registration->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Current user is not the owner of the given reservation.');
        }

        if (!$registration->isConfirmed()) {
            return $this->redirectToRoute('event_register', [
                'slug' => $registration->getEvent()->getSlug(),
            ]);
        }

        return $this->render('event/payment_successful.html.twig', [
            'registration' => $registration,
        ]);
    }
}
