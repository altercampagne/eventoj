<?php

declare(strict_types=1);

namespace App\Admin\Controller\Stage;

use App\Admin\Form\StageFormType;
use App\Admin\Security\Permission;
use App\Entity\Event;
use App\Entity\Stage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CreateOrUpdateController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[IsGranted(Permission::STAGE_CREATE->value)]
    #[Route('/stages/create/{slug}', name: 'admin_stage_create')]
    public function create(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Event $event,
    ): Response {
        $stage = new Stage($event);
        if (null !== $lastStage = $event->getLastStage()) {
            $stage->setDate($lastStage->getDate()->modify('+1 day'));
        }

        return $this->update($request, $stage, true);
    }

    #[IsGranted(Permission::STAGE_UPDATE->value, 'stage')]
    #[Route('/stages/{slug}/update/{backToStage}', name: 'admin_stage_update')]
    public function update(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Stage $stage,
        bool $creation = false,
        bool $backToStage = false,
    ): Response {
        $form = $this->createForm(StageFormType::class, $stage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($stage->getPreparers() as $preparer) {
                $preparer->addRole('ROLE_PREPA');
                $this->em->persist($preparer);
            }

            $this->em->persist($stage);
            $this->em->flush();

            $this->addFlash('success', \sprintf('L\'étape a bien été %s ! 🥳', $creation ? 'créée' : 'modifiée'));

            if ($backToStage) {
                return $this->redirectToRoute('event_stage_show', ['event_slug' => $stage->getEvent()->getSlug(), 'stage_slug' => $stage->getSlug()]);
            }

            return $this->redirectToRoute('admin_stage_show', ['slug' => $stage->getSlug()]);
        }

        return $this->render('admin/stage/edit.html.twig', [
            'creation' => $creation,
            'stage' => $stage,
            'form' => $form->createView(),
        ]);
    }
}
