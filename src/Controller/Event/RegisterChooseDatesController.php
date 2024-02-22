<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\Meal;
use App\Entity\Registration;
use App\Entity\StageRegistration;
use App\Entity\User;
use App\Form\EventRegistration\ChooseDatesFormType;
use App\Form\EventRegistration\EventRegistrationDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/event/{slug}/register/dates', name: 'event_register_choose_dates')]
class RegisterChooseDatesController extends AbstractController
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

        if (!$event->isAT()) {
            return $this->redirectToRoute('event_register_choose_price', ['slug' => $event->getSlug()]);
        }

        /** @var User $user */
        $user = $this->getUser();

        if (null === $registration = $this->em->getRepository(Registration::class)->findOngoingRegistrationForEventAndUser($event, $user)) {
            return $this->redirectToRoute('event_register', ['slug' => $event->getSlug()]);
        }

        $eventRegistrationDTO = new EventRegistrationDTO($registration);
        $form = $this->createForm(ChooseDatesFormType::class, $eventRegistrationDTO, [
            'registration' => $registration,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $bookedStages = $eventRegistrationDTO->getBookedStages();
            $stagesRegistrations = [];
            for ($i = 0; $i < \count($bookedStages); ++$i) {
                $stageRegistration = new StageRegistration(stage: $bookedStages[$i], registration: $registration);

                if (0 === $i) {
                    switch ($eventRegistrationDTO->firstMeal) {
                        case Meal::DINNER:
                            $stageRegistration->setPresentForLunch(false);
                            // no break
                        case Meal::LUNCH:
                            $stageRegistration->setPresentForBreakfast(false);
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
                ->setNeededBike($eventRegistrationDTO->neededBike)
            ;

            $this->em->persist($registration);
            $this->em->flush();

            return $this->redirectToRoute('event_register_choose_price', ['slug' => $event->getSlug()]);
        }

        return $this->render('event/register_choose_dates.html.twig', [
            'registration' => $registration,
            'form' => $form,
        ]);
    }
}
