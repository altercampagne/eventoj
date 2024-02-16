<?php

declare(strict_types=1);

namespace App\Admin\Controller\Event;

use App\Admin\Security\Permission;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::EVENT_LIST->value)]
#[Route('/events', name: 'admin_event_list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('admin/event/list.html.twig', [
            'events' => $this->em->getRepository(Event::class)->findBy([], ['createdAt' => 'DESC']),
        ]);
    }
}
