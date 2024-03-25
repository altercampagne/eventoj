<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Service\MealOverview\MealAggregator;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event/{slug}/meals', name: 'event_meal_overview')]
class MealOverviewController extends AbstractController
{
    public function __construct(
        private readonly MealAggregator $mealAggregator,
    ) {
    }

    public function __invoke(
        #[MapEntity(expr: 'repository.findOneBySlugJoinedWithStagesAndAlternatives(slug)')]
        Event $event,
    ): Response {
        if (!$event->isPublished()) {
            throw $this->createNotFoundException();
        }

        return $this->render('event/meal_overview.html.twig', [
            'event' => $event,
            'overview' => $this->mealAggregator->aggregate($event),
        ]);
    }
}
