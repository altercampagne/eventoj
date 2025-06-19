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

#[IsGranted(Permission::EVENT_FOOD_CALCULATOR->value, 'event')]
#[Route('/events/{slug}/food-calculator', name: 'admin_event_food_calculator')]
class FoodCalculatorController extends AbstractController
{
    public function __invoke(
        #[MapEntity(expr: 'repository.findOneBySlugJoinedToAllChildEntities(slug)')]
        Event $event,
    ): Response {
        if (!$event->isPublished()) {
            $this->addFlash('warning', "Le calculateur de nourriture n'est disponible qu'une fois l'évènement publié.");

            return $this->redirectToRoute('admin_event_show', ['slug' => $event->getSlug()]);
        }

        return $this->render('admin/event/food_calculator.html.twig', [
            'event' => $event,
        ]);
    }
}
