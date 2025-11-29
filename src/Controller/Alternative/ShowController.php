<?php

declare(strict_types=1);

namespace App\Controller\Alternative;

use App\Entity\Alternative;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/alternative/{slug}', name: 'alternative_show')]
class ShowController extends AbstractController
{
    public function __invoke(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Alternative $alternative,
    ): Response {
        return $this->render('alternative/show.html.twig', [
            'alternative' => $alternative,
        ]);
    }
}
