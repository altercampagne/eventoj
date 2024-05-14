<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\Registration;
use App\Entity\User;
use App\Form\EventRegistration\ChoosePeopleFormType;
use App\Form\EventRegistration\EventRegistrationDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

#[Route('/event/{slug}/register', name: 'event_register')]
#[Route('/event/{slug}/register', name: 'event_register_choose_people')]
class RegisterChoosePeopleController extends AbstractController
{
    use TargetPathTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Event $event,
    ): Response {
        if (!$event->isBookable()) {
            throw $this->createNotFoundException();
        }
        if ($event->isFull()) {
            $this->addFlash('warning', 'Cet évènement est complet, les inscriptions sont fermées !');

            return $this->redirectToRoute('event_show', ['slug' => $event->getSlug()]);
        }
        if (!$this->isGranted('ROLE_USER')) {
            $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl('event_register', ['slug' => $event->getSlug()]));

            return $this->redirectToRoute('event_registration_need_account', ['slug' => $event->getSlug()]);
        }

        /** @var User $user */
        $user = $this->getUser();

        if (null === $user->getDiet()) {
            $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl('event_register', ['slug' => $event->getSlug()]));

            $this->addFlash('warning', 'Merci de remplir ton profil avant de pouvoir t\'inscrire !');

            return $this->redirectToRoute('profile_update_profile');
        }

        if ($event->isEB()) {
            // In case of an EB, we can have at most 1 registration per user!
            if (null !== $registration = $this->em->getRepository(Registration::class)->findConfirmedRegistrationForEventAndUser($event, $user)) {
                $this->addFlash('warning', 'Tu as déjà inscrit pour cet évènement, ta place se trouve ici ! :)');

                return $this->redirectToRoute('profile_registrations');
            }
        }

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
