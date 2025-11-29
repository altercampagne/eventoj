<?php

declare(strict_types=1);

namespace App\Admin\Controller\Event;

use App\Admin\Security\Permission;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::EVENT_VIEW_STAGES->value, 'event')]
#[Route('/events/{slug}', name: 'admin_event_show')]
#[Route('/events/{slug}', name: 'admin_event_show_stages')]
class ShowTabStagesController extends AbstractController
{
    public function __invoke(
        #[MapEntity(expr: 'repository.findOneBySlugJoinedWithStagesAndAlternatives(slug)')]
        Event $event,
    ): Response {
        return $this->render('admin/event/show_tab/stages.html.twig', [
            'event' => $event,
        ]);
    }
}
