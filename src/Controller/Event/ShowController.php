<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event/{slug}', name: 'event_show')]
class ShowController extends AbstractController
{
    public function __invoke(
        #[MapEntity(expr: 'repository.findOneBySlugJoinedWithStagesAndAlternatives(slug)')]
        Event $event,
    ): Response {
        if (!$event->isPublished()) {
            throw $this->createNotFoundException();
        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }
}
