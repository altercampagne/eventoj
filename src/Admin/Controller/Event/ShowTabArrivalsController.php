<?php

declare(strict_types=1);

namespace App\Admin\Controller\Event;

use App\Admin\Security\Permission;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// @TODO: This controller is absolutely not optimized (too many DB queries, 1
// per registration!). This is OK for now but should be improved.
#[IsGranted(Permission::EVENT_VIEW_ARRIVALS->value, 'event')]
#[Route('/events/{slug}/arrivals', name: 'admin_event_show_arrivals')]
class ShowTabArrivalsController extends AbstractController
{
    public function __invoke(
        #[MapEntity(expr: 'repository.findOneBySlugJoinedToAllChildEntities(slug)')]
        Event $event,
        #[MapQueryParameter]
        bool $showPastStages = false,
    ): Response {
        return $this->render('admin/event/show_tab/arrivals.html.twig', [
            'event' => $event,
            'showPastStages' => $showPastStages,
        ]);
    }
}
