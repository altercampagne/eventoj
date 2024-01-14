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
#[Route('/event/{slug}/register/{id?}', name: 'event_register')]
class RegisterController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Event $event,
        #[MapEntity(mapping: ['id' => 'id'])]
        Registration $registration = null,
    ): Response {
        if (!$event->isBookable()) {
            throw $this->createNotFoundException();
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

            $startIndex = (int) array_search($eventRegistrationDTO->stageStart, $stages, true);

            $stages = \array_slice(
                $stages,
                $startIndex,
                (int) array_search($eventRegistrationDTO->stageEnd, $stages, true) - $startIndex + 1,
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

        return $this->render('event/register.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }
}
