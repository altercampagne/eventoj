<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Admin\Security\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::DEBUG->value)]
#[Route('/debug', name: 'admin_debug')]
class DebugController extends AbstractController
{
    public function __invoke(?string $slug = null): Response
    {
        return $this->render('admin/debug.html.twig', [
            'now' => new \DateTimeImmutable(),
            'timezone' => date_default_timezone_get(),
        ]);
    }
}
