<?php

declare(strict_types=1);

namespace App\Controller\Registration;

use App\Entity\Registration;
use App\Service\Registration\RegistrationCanceller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/registration/{id}/cancel', name: 'registration_cancel', methods: 'POST')]
class CancelController extends AbstractController
{
    public function __construct(
        private readonly RegistrationCanceller $registrationCanceller,
    ) {
    }

    public function __invoke(Registration $registration): Response
    {
        try {
            $this->registrationCanceller->cancel($registration);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException(previous: $e);
        }

        $this->addFlash('warning', 'Ton inscription a bien été annulée. On espère te voir à un autre évènement !');

        return $this->redirectToRoute('profile_registrations');
    }
}
