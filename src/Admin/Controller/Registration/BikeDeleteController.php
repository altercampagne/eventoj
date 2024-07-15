<?php

declare(strict_types=1);

namespace App\Admin\Controller\Registration;

use App\Admin\Security\Permission;
use App\Entity\Registration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::REGISTRATION_BIKE_DELETE->value, 'registration')]
#[Route('/registrations/{id}/bike_delete', name: 'admin_registration_bike_delete', requirements: ['id' => Requirement::UUID_V4], methods: 'POST')]
class BikeDeleteController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Registration $registration): Response
    {
        if ($registration->getNeededBike() <= 0) {
            $this->addFlash('danger', 'Il n\'y a pas de vélo de prêt à supprimer pour cette inscription.');

            return $this->redirectToRoute('admin_registration_show', ['id' => $registration->getId()]);
        }

        $registration->setNeededBike($registration->getNeededBike() - 1);

        $this->em->persist($registration);
        $this->em->flush();

        $this->addFlash('success', 'Un vélo a été supprimé de cette inscription.');

        return $this->redirectToRoute('admin_registration_show', ['id' => $registration->getId()]);
    }
}
