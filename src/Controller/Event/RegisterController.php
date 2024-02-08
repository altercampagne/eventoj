<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\Meal;
use App\Entity\Registration;
use App\Entity\StageRegistration;
use App\Entity\User;
use App\Form\EventRegistrationDTO;
use App\Form\EventRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/event/{slug}/register', name: 'event_register')]
class RegisterController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(
        Request $request,
        string $slug,
    ): Response {
        $event = $this->em->getRepository(Event::class)->findOneBySlugJoinedToAllChildEntities($slug);
        if (null === $event || !$event->isBookable()) {
            throw $this->createNotFoundException();
        }

        /** @var User $user */
        $user = $this->getUser();

        $registration = $this->em->getRepository(Registration::class)->findOngoingRegistrationForEventAndUser($event, $user);

        $eventRegistrationDTO = new EventRegistrationDTO($event, $registration);
        $form = $this->createForm(EventRegistrationFormType::class, $eventRegistrationDTO, [
            'event' => $event,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            if (null === $registration) {
                $registration = new Registration(
                    user: $user,
                    event: $event,
                );
            }

            $bookedStages = $eventRegistrationDTO->getBookedStages();
            $stagesRegistrations = [];
            for ($i = 0; $i < \count($bookedStages); ++$i) {
                $stageRegistration = new StageRegistration(stage: $bookedStages[$i], registration: $registration);

                if (0 === $i) {
                    switch ($eventRegistrationDTO->firstMeal) {
                        case Meal::LUNCH:
                            $stageRegistration->setPresentForBreakfast(false);
                            // no break
                        case Meal::DINNER:
                            $stageRegistration->setPresentForLunch(false);
                    }
                } elseif ($i === \count($bookedStages) - 1) {
                    switch ($eventRegistrationDTO->lastMeal) {
                        case Meal::BREAKFAST:
                            $stageRegistration->setPresentForLunch(false);
                            // no break
                        case Meal::LUNCH:
                            $stageRegistration->setPresentForDinner(false);
                    }
                }

                $stagesRegistrations[] = $stageRegistration;
            }

            $registration
                ->setStagesRegistrations($stagesRegistrations)
                ->setPricePerDay($eventRegistrationDTO->pricePerDay * 100)
                ->setNeedBike($eventRegistrationDTO->needBike)
            ;

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
