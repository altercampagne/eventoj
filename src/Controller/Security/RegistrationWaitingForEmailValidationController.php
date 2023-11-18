<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/registration-almost-successful', name: 'registration_waiting_for_email_validation')]
class RegistrationWaitingForEmailValidationController extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        return $this->render('security/registration_waiting_for_email_validation.html.twig');
    }
}
