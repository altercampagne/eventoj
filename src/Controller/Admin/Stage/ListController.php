<?php

declare(strict_types=1);

namespace App\Controller\Admin\Stage;

use App\Entity\Event;
use App\Entity\Stage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/_admin/stages/{slug}', name: 'admin_stage_list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Event $event): Response
    {
        return $this->render('admin/stage/list.html.twig', [
            'event' => $event,
            'stages' => $this->em->getRepository(Stage::class)->findBy(['event' => $event], ['createdAt' => 'DESC']),
        ]);
    }
}
