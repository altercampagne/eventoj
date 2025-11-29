<?php

declare(strict_types=1);

namespace App\Admin\Controller\Registration;

use App\Admin\Security\Permission;
use App\Entity\Registration;
use App\Service\Availability\StageAvailability;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::REGISTRATION_BIKE_ADD->value, 'registration')]
#[Route('/_admin/registrations/{id}/bike_add', name: 'admin_registration_bike_add', requirements: ['id' => Requirement::UUID], methods: 'POST')]
class BikeAddController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Registration $registration): Response
    {
        if ($registration->getNeededBike() >= $registration->countPeople()) {
            $this->addFlash('danger', 'Impossible de demander plus de vélos que de personnes !');

            return $this->redirectToRoute('admin_registration_show', ['id' => $registration->getId()]);
        }

        foreach ($registration->getStagesRegistrations() as $stageRegistration) {
            $availability = new StageAvailability($stageRegistration->getStage());

            foreach ($availability->getMealAvailabilities() as $mealAvailability) {
                if ($mealAvailability->bikes->availability <= 0) {
                    $this->addFlash('danger', 'Plus de vélo disponible sur cette période.');

                    return $this->redirectToRoute('admin_registration_show', ['id' => $registration->getId()]);
                }
            }
        }

        $registration->setNeededBike($registration->getNeededBike() + 1);

        $this->em->persist($registration);
        $this->em->flush();

        $this->addFlash('success', 'Un vélo a été ajouté à l\'inscription.');

        return $this->redirectToRoute('admin_registration_show', ['id' => $registration->getId()]);
    }
}
