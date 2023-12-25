<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\Registration;
use App\Entity\User;
use App\Form\EventRegistrationDTO;
use App\Form\EventRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class RegisterController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('/event/{slug}/register/{id?}', name: 'event_register')]
    public function __invoke(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Event $event,
        #[MapEntity(mapping: ['id' => 'id'])]
        Registration $registration = null,
    ): Response {
        if (!$event->isBookable()) {
            return $this->redirectToRoute('event_show', ['slug' => $event->getSlug()]);
        }

        $eventRegistrationDTO = new EventRegistrationDTO($event);
        if (null !== $registration) {
            $eventRegistrationDTO->configureFromRegistration($registration);
        }

        $form = $this->createForm(EventRegistrationFormType::class, $eventRegistrationDTO, [
            'event' => $event,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $stages = $event->getStages()->toArray();

            $stages = \array_slice(
                $stages,
                (int) array_search($eventRegistrationDTO->stageStart, $stages, true),
                (int) array_search($eventRegistrationDTO->stageEnd, $stages, true),
            );

            $registration = new Registration(
                user: $user,
                event: $event,
                stages: $stages,
                firstMeal: $eventRegistrationDTO->firstMeal,
                lastMeal: $eventRegistrationDTO->lastMeal,
                pricePerDay: $eventRegistrationDTO->pricePerDay * 100,
                needBike: $eventRegistrationDTO->needBike,
            );

            $this->em->persist($registration);
            $this->em->flush();

            return $this->redirectToRoute('event_registration_pay', ['id' => (string) $registration->getId()]);
        }

        return $this->render('event/book.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }
}
