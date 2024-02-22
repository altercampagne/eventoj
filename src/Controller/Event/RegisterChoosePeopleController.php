<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\Registration;
use App\Entity\User;
use App\Form\EventRegistration\ChoosePeopleFormType;
use App\Form\EventRegistration\EventRegistrationDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/event/{slug}/register', name: 'event_register')]
#[Route('/event/{slug}/register', name: 'event_register_choose_people')]
class RegisterChoosePeopleController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request, Event $event): Response
    {
        if (!$event->isBookable()) {
            throw $this->createNotFoundException();
        }

        /** @var User $user */
        $user = $this->getUser();

        if (null === $registration = $this->em->getRepository(Registration::class)->findOngoingRegistrationForEventAndUser($event, $user)) {
            $registration = new Registration($user, $event);
        }

        $eventRegistrationDTO = new EventRegistrationDTO($registration);
        $form = $this->createForm(ChoosePeopleFormType::class, $eventRegistrationDTO, [
            'registration' => $registration,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $registration
                ->setNeededBike($eventRegistrationDTO->neededBike)
                ->setCompanions($eventRegistrationDTO->companions)
            ;

            $this->em->persist($registration);
            $this->em->flush();

            if ($event->isAT()) {
                return $this->redirectToRoute('event_register_choose_dates', ['slug' => $event->getSlug()]);
            }

            return $this->redirectToRoute('event_register_choose_price', ['slug' => $event->getSlug()]);
        }

        return $this->render('event/register_choose_people.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }
}
