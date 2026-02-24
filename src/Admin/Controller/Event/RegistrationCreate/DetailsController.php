<?php

declare(strict_types=1);

namespace App\Admin\Controller\Event\RegistrationCreate;

use App\Admin\Form\RegistrationCreate\DetailsFormDTO;
use App\Admin\Form\RegistrationCreate\DetailsFormType;
use App\Admin\Security\Permission;
use App\Entity\Event;
use App\Entity\Registration;
use App\Entity\StageRegistration;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::REGISTRATION_CREATE->value)]
#[Route('/_admin/events/{slug}/registration_create/{id}', name: 'admin_registration_create_details')]
class DetailsController extends AbstractController
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
        User $user,
    ): Response {
        $dto = new DetailsFormDTO(event: $event, user: $user);

        $form = $this->createForm(DetailsFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dto = $form->getData();

            /** @var User */
            $currentUser = $this->getUser();
            $registration = new Registration($user, $event, $currentUser);
            $registration->setNeededBike($dto->bikes);
            $registration->setCompanions($dto->companions);
            $registration->setPrice($dto->price);

            $stagesRegistrations = [];
            foreach ($event->getStages() as $stage) {
                if ($stage->getDate() < $dto->start || $stage->getDate() > $dto->end) {
                    continue;
                }

                $stageRegistration = new StageRegistration($stage, $registration);
                if ($stage->getDate()->getTimestamp() === $dto->start->getTimestamp()) {
                    if ($dto->firstMeal->isDinner()) {
                        $stageRegistration->setPresentForBreakfast(false);
                        $stageRegistration->setPresentForLunch(false);
                    } elseif ($dto->firstMeal->isLunch()) {
                        $stageRegistration->setPresentForBreakfast(false);
                    }
                } elseif ($stage->getDate()->getTimestamp() === $dto->end->getTimestamp()) {
                    if ($dto->firstMeal->isBreakfast()) {
                        $stageRegistration->setPresentForLunch(false);
                        $stageRegistration->setPresentForDinner(false);
                    } elseif ($dto->firstMeal->isLunch()) {
                        $stageRegistration->setPresentForDinner(false);
                    }
                }

                $this->em->persist($stageRegistration);
                $stagesRegistrations[] = $stageRegistration;
            }

            $registration->setStagesRegistrations($stagesRegistrations);
            $registration->confirm();

            $this->em->persist($registration);
            $this->em->flush();

            $this->addFlash('success', "L'inscription de {$user->getPublicName()} pour l'évènement {$event->getName()} a été créée avec succès !");

            return $this->redirectToRoute('admin_registration_show', ['id' => $registration->getId()]);
        }

        return $this->render('admin/event/registration_create/details.html.twig', [
            'event' => $event,
            'user' => $user,
            'form' => $form,
        ]);
    }
}
