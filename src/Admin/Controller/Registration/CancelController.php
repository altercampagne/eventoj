<?php

declare(strict_types=1);

namespace App\Admin\Controller\Registration;

use App\Admin\Security\Permission;
use App\Entity\Registration;
use App\Service\Registration\RegistrationCanceller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::REGISTRATION_CANCEL->value, 'registration')]
#[Route('/_admin/registrations/{id}/cancel', name: 'admin_registration_cancel', requirements: ['id' => Requirement::UUID], methods: 'POST')]
class CancelController extends AbstractController
{
    public function __construct(
        private readonly RegistrationCanceller $registrationCanceller,
    ) {
    }

    public function __invoke(Registration $registration): Response
    {
        try {
            $registrationCancellationResult = $this->registrationCanceller->cancel($registration, cancelledByAdmin: true);
        } catch (\InvalidArgumentException) {
            $this->addFlash('error', 'L\'inscription ne peut pas être annulée.');

            return $this->redirectToRoute('admin_registration_show', ['id' => $registration->getId()]);
        }

        $message = 'L\'inscription a bien été annulée';
        if ($registrationCancellationResult->haveBeenRefunded()) {
            $message .= ' (et remboursée)';
        }

        $this->addFlash('info', $message.' !');

        return $this->redirectToRoute('admin_registration_show', ['id' => $registration->getId()]);
    }
}
