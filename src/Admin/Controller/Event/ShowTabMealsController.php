<?php

declare(strict_types=1);

namespace App\Admin\Controller\Event;

use App\Admin\Security\Permission;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::EVENT_VIEW_MEALS->value, 'event')]
#[Route('/events/{slug}/meals', name: 'admin_event_show_meals')]
class ShowTabMealsController extends AbstractController
{
    public function __invoke(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Event $event,
    ): Response {
        return $this->render('admin/event/show_tab/meals.html.twig', [
            'event' => $event,
        ]);
    }
}
