<?php

declare(strict_types=1);

namespace App\Admin\Controller\Alternative;

use App\Admin\Security\Permission;
use App\Entity\Alternative;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::ALTERNATIVE_DELETE->value, 'alternative')]
#[Route('/alternatives/{slug}/delete', name: 'admin_alternative_delete', methods: 'POST')]
class DeleteController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Alternative $alternative,
    ): Response {
        if ($alternative->isUsed()) {
            $this->addFlash('error', 'L\'alternative est liée à une ou plusieurs étapes et ne peut être supprimée !');
        }

        $this->em->remove($alternative);
        $this->em->flush();

        $this->addFlash('success', 'L\'alternative a été supprimée !');

        return $this->redirectToRoute('admin_alternative_list');
    }
}
