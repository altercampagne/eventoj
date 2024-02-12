<?php

declare(strict_types=1);

namespace App\Controller\Admin\Event;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/_admin/events/{slug}', name: 'admin_event_show')]
class ShowController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(string $slug): Response
    {
        return $this->render('admin/event/show.html.twig', [
            'event' => $this->em->getRepository(Event::class)->findOneBySlugJoinedToAllChildEntities($slug),
        ]);
    }
}
