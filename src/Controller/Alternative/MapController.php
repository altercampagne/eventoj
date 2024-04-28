<?php

declare(strict_types=1);

namespace App\Controller\Alternative;

use App\Entity\Alternative;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/alternatives', name: 'alternative_map')]
class MapController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('alternative/map.html.twig', [
            'alternatives' => $this->em->getRepository(Alternative::class)->findAll(),
        ]);
    }
}
