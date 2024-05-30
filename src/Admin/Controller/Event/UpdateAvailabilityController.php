<?php

declare(strict_types=1);

namespace App\Admin\Controller\Event;

use App\Admin\Controller\Util\RedirectorTrait;
use App\Admin\Security\Permission;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::EVENT_UPDATE_AVAILABILITY->value, 'event')]
#[Route('/events/{slug}/update_availability', name: 'admin_event_update_availability')]
class UpdateAvailabilityController extends AbstractController
{
    use RedirectorTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Event $event,
    ): Response {
        foreach ($event->getStages() as $stage) {
            $stage->updateIsFullProperty();
            $this->em->persist($stage);
        }

        $this->em->flush();

        $this->addFlash('success', "Les disponibilités de l'évènement ont été mises à jour !");

        return $this->redirectToRefererOrToRoute('admin_event_show', ['slug' => $event->getSlug()]);
    }
}
