<?php

declare(strict_types=1);

namespace App\Admin\Controller\Event\RegistrationCreate;

use App\Admin\Form\RegistrationCreate\ChooseUserFormType;
use App\Admin\Security\Permission;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::REGISTRATION_CREATE->value)]
#[Route('/_admin/events/{slug}/registration_create', name: 'admin_registration_create')]
class ChooseUserController extends AbstractController
{
    public function __invoke(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Event $event,
    ): Response {
        if (!$event->isPublished()) {
            $this->addFlash('error', 'Impossible de créer une inscription pour un évènement non publié.');

            return $this->redirectToRoute('admin_event_show', [
                'slug' => $event->getSlug(),
            ]);
        }

        $form = $this->createForm(ChooseUserFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            \assert(null !== $data);

            return $this->redirectToRoute('admin_registration_create_details', [
                'slug' => $event->getSlug(),
                'id' => $data->user->getId(),
            ]);
        }

        return $this->render('admin/event/registration_create/choose_user.html.twig', [
            'form' => $form,
        ]);
    }
}
