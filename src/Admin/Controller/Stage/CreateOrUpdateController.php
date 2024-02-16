<?php

declare(strict_types=1);

namespace App\Admin\Controller\Stage;

use App\Admin\Form\StageFormType;
use App\Admin\Security\Permission;
use App\Entity\Event;
use App\Entity\Stage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CreateOrUpdateController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[IsGranted(Permission::STAGE_CREATE->value)]
    #[Route('/stages/create/{slug}', name: 'admin_stage_create')]
    public function create(Request $request, Event $event): Response
    {
        $stage = new Stage($event);
        if (null !== $lastStage = $event->getLastStage()) {
            $stage->setDate($lastStage->getDate()->modify('+1 day'));
        }

        return $this->update($request, $stage, true);
    }

    #[IsGranted(Permission::STAGE_UPDATE->value, 'stage')]
    #[Route('/stages/{slug}/update', name: 'admin_stage_update')]
    public function update(Request $request, Stage $stage, bool $creation = false): Response
    {
        $form = $this->createForm(StageFormType::class, $stage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($stage);
            $this->em->flush();

            $this->addFlash('success', sprintf('L\'étape a bien été %s ! 🥳', $creation ? 'créée' : 'modifiée'));

            return $this->redirectToRoute('admin_event_show', ['slug' => $stage->getEvent()->getSlug()]);
        }

        return $this->render('admin/stage/edit.html.twig', [
            'creation' => $creation,
            'stage' => $stage,
            'form' => $form->createView(),
        ]);
    }
}
