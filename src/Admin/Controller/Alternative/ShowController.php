<?php

declare(strict_types=1);

namespace App\Admin\Controller\Alternative;

use App\Admin\Security\Permission;
use App\Entity\Alternative;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::ALTERNATIVE_VIEW->value, 'alternative')]
#[Route('/alternatives/{slug}', name: 'admin_alternative_show')]
class ShowController extends AbstractController
{
    public function __invoke(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Alternative $alternative,
    ): Response {
        return $this->render('admin/alternative/show.html.twig', [
            'alternative' => $alternative,
        ]);
    }
}
