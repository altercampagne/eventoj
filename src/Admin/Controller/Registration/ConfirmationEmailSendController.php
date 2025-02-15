<?php

declare(strict_types=1);

namespace App\Admin\Controller\Registration;

use App\Admin\Security\Permission;
use App\Email\RegistrationConfirmationSender;
use App\Entity\Registration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::REGISTRATION_CONFIRMATION_EMAIL_SEND->value, 'registration')]
#[Route('/registrations/{id}/confirmation_email_send', name: 'admin_registration_confirmation_email_send', requirements: ['id' => Requirement::UUID_V4], methods: 'POST')]
final class ConfirmationEmailSendController extends AbstractController
{
    public function __construct(
        private readonly RegistrationConfirmationSender $registrationConfirmationSender,
    ) {
    }

    public function __invoke(Registration $registration): Response
    {
        if (!$registration->isConfirmed()) {
            $this->addFlash('danger', "L'inscription n'est pas confirmée, le mail n'a pas été envoyé.");

            return $this->redirectToRoute('admin_registration_show', ['id' => $registration->getId()]);
        }

        $this->registrationConfirmationSender->send($registration);

        $this->addFlash('success', 'Le mail de confirmation a bien été envoyé.');

        return $this->redirectToRoute('admin_registration_show', ['id' => $registration->getId()]);
    }
}
