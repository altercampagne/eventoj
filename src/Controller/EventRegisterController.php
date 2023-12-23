<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventRegistrationDTO;
use App\Form\EventRegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class EventRegisterController extends AbstractController
{
    #[Route('/event/{slug}/register', name: 'event_register')]
    public function __invoke(Event $event, Request $request): Response
    {
        if (!$event->isBookable()) {
            return $this->redirectToRoute('event_show', ['slug' => $event->getSlug()]);
        }

        $eventRegistrationDTO = new EventRegistrationDTO($event);

        $form = $this->createForm(EventRegistrationFormType::class, $eventRegistrationDTO, [
            'event' => $event,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
        }

        return $this->render('event/book.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }
}
