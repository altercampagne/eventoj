<?php

declare(strict_types=1);

namespace App\Controller\Alternative;

use App\Entity\Alternative;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/alternative/{slug}', name: 'alternative_show')]
class ShowController extends AbstractController
{
    public function __invoke(Alternative $alternative): Response
    {
        return $this->render('alternative/show.html.twig', [
            'alternative' => $alternative,
        ]);
    }
}
