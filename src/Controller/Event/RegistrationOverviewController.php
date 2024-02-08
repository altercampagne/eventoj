<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Registration;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/registration/{id}/overview', name: 'event_registration_overview')]
class RegistrationOverviewController extends AbstractController
{
    public function __construct(
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

            $this->logger->warning('Trying to see overview of a registration which is not in waiting payment!', [
                'registration_id' => (string) $registration->getId(),
            ]);

            return $this->redirectToRoute('homepage');
        }

        return $this->render('event/registration_overview.html.twig', [
            'registration' => $registration,
        ]);
    }
}
