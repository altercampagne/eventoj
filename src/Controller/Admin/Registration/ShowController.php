<?php

declare(strict_types=1);

namespace App\Controller\Admin\Registration;

use App\Entity\Registration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/_admin/registrations/{id}', name: 'admin_registration_show')]
class ShowController extends AbstractController
{
    public function __invoke(Registration $registration): Response
    {
        return $this->render('admin/registration/show.html.twig', [
            'registration' => $registration,
        ]);
    }
}
