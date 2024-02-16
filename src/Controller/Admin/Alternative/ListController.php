<?php

declare(strict_types=1);

namespace App\Controller\Admin\Alternative;

use App\Entity\Alternative;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/alternatives', name: 'admin_alternative_list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('admin/alternative/list.html.twig', [
            'alternatives' => $this->em->getRepository(Alternative::class)->findAllJoinedToEvents(),
        ]);
    }
}
