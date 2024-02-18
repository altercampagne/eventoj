<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\Stage;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event/{event_slug}/{stage_slug}', name: 'event_stage_show')]
class StageShowController extends AbstractController
{
    public function __invoke(
        #[MapEntity(expr: 'repository.findOneBySlugJoinedWithStagesAndAlternatives(event_slug)')]
        Event $event,
        #[MapEntity(mapping: ['stage_slug' => 'slug'])]
        Stage $stage,
    ): Response {
        if (!$event->isPublished()) {
            throw $this->createNotFoundException();
        }
        if ($event !== $stage->getEvent()) {
            throw $this->createNotFoundException();
        }

        return $this->render('event/stage_show.html.twig', [
            'previous_stage' => $event->getPreviousStage($stage),
            'stage' => $stage,
            'next_stage' => $event->getNextStage($stage),
        ]);
    }
}
