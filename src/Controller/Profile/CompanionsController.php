<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/me/companions', name: 'profile_companions')]
class CompanionsController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('profile/companions.html.twig');
    }
}
