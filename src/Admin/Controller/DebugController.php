<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Admin\Security\Permission;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::DEBUG->value)]
#[Route('/debug', name: 'admin_debug')]
class DebugController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $debugLogger,
    ) {
    }

    public function __invoke(?string $slug = null): Response
    {
        $this->debugLogger->debug('This is a debug test from debug controller', [
            'user' => $this->getUser(),
        ]);

        return $this->render('admin/debug.html.twig', [
            'now' => new \DateTimeImmutable(),
            'timezone' => date_default_timezone_get(),
        ]);
    }
}
