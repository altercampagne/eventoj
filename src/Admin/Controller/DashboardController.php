<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Admin\Security\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::ADMIN_ACCESS->value)]
#[Route('/', name: 'admin')]
class DashboardController extends AbstractController
{
    public function __invoke(?string $slug = null): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }
}
