<?php

declare(strict_types=1);

namespace App\Admin\Controller\Stage;

use App\Admin\Controller\Util\RedirectorTrait;
use App\Admin\Security\Permission;
use App\Entity\Stage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::STAGE_UPDATE_AVAILABILITY->value, 'stage')]
#[Route('/stages/{slug}/update_availability', name: 'admin_stage_update_availability')]
class UpdateAvailabilityController extends AbstractController
{
    use RedirectorTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Stage $stage,
    ): Response {
        $stage->updateIsFullProperty();

        $this->em->persist($stage);
        $this->em->flush();

        $this->addFlash('success', 'Les disponibilités de cette étape ont été mises à jour !');

        return $this->redirectToRefererOrToRoute('admin_stage_show', ['slug' => $stage->getSlug()]);
    }
}
