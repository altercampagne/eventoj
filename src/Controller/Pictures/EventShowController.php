<?php

declare(strict_types=1);

namespace App\Controller\Pictures;

use App\Entity\Event;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/pictures/{slug}', name: 'pictures_event')]
class EventShowController extends AbstractController
{
    public function __invoke(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Event $event,
    ): Response {
        return $this->render('pictures/event_show.html.twig', [
            'event' => $event,
        ]);
    }
}
