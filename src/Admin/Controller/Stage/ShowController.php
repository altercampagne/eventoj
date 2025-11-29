<?php

declare(strict_types=1);

namespace App\Admin\Controller\Stage;

use App\Admin\Security\Permission;
use App\Entity\Stage;
use App\Service\Availability\StageAvailability;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::STAGE_VIEW->value, 'stage')]
#[Route('/stages/{slug}', name: 'admin_stage_show')]
class ShowController extends AbstractController
{
    public function __invoke(
        #[MapEntity(expr: 'repository.findOneBySlugJoinedToAllChildEntities(slug)')]
        Stage $stage,
    ): Response {
        return $this->render('admin/stage/show.html.twig', [
            'stage' => $stage,
            'availability' => new StageAvailability($stage),
        ]);
    }
}
