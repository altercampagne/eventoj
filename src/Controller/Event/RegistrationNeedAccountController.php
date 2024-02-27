<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event/registration_need_account/{slug}', name: 'event_registration_need_account')]
class RegistrationNeedAccountController extends AbstractController
{
    public function __invoke(Event $event): Response
    {
        return $this->render('event/registration_need_account.html.twig', [
            'event' => $event,
        ]);
    }
}
