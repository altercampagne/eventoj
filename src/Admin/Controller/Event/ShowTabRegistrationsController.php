<?php

declare(strict_types=1);

namespace App\Admin\Controller\Event;

use App\Entity\Event;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/events/{slug}/registrations', name: 'admin_event_show_registrations')]
class ShowTabRegistrationsController extends AbstractController
{
    public function __invoke(
        #[MapEntity(expr: 'repository.findOneBySlugJoinedToAllChildEntities(slug)')]
        Event $event,
    ): Response {
        return $this->render('admin/event/show_tab/registrations.html.twig', [
            'event' => $event,
        ]);
    }
}
