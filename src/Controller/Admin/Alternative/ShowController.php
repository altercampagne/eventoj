<?php

declare(strict_types=1);

namespace App\Controller\Admin\Alternative;

use App\Entity\Alternative;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/_admin/alternatives/{slug}', name: 'admin_alternative_show')]
class ShowController extends AbstractController
{
    public function __invoke(Alternative $alternative): Response
    {
        return $this->render('admin/alternative/show.html.twig', [
            'alternative' => $alternative,
        ]);
    }
}
