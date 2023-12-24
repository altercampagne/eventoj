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
class PayController extends AbstractController
{
    #[Route('/registration/{id}/pay', name: 'event_registration_pay')]
    public function __invoke(Request $request, Registration $registration): Response
    {
        if (!$registration->canBeConfirmed()) {
            $this->addFlash('error', 'Cette réservation ne peut plus être confirmée.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('event/pay.html.twig', [
            'registration' => $registration,
        ]);
    }
}
