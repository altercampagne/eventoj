<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/me/memberships', name: 'profile_memberships')]
class MembershipsController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('profile/memberships.html.twig');
    }
}
