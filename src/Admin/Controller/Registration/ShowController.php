<?php

declare(strict_types=1);

namespace App\Admin\Controller\Registration;

use App\Admin\Security\Permission;
use App\Entity\Registration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::REGISTRATION_VIEW->value, 'registration')]
#[Route('/registrations/{id}', name: 'admin_registration_show', requirements: ['id' => Requirement::UUID_V4])]
class ShowController extends AbstractController
{
    public function __invoke(Registration $registration): Response
    {
        return $this->render('admin/registration/show.html.twig', [
            'registration' => $registration,
        ]);
    }
}
