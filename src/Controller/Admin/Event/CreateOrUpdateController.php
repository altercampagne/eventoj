<?php

declare(strict_types=1);

namespace App\Controller\Admin\Event;

use App\Entity\Event;
use App\Entity\EventType;
use App\Form\Admin\EventFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class CreateOrUpdateController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/_admin/events/create/{type}', name: 'admin_event_create')]
    public function create(Request $request, EventType $type): Response
    {
        return $this->update($request, Event::createFromType($type), true);
    }

    #[Route('/_admin/events/{slug}/update', name: 'admin_event_update')]
    public function update(Request $request, Event $event, bool $creation = false): Response
    {
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($event);
            $this->em->flush();

            $this->addFlash('success', sprintf('L\'évènement a bien été %s ! 🥳', $creation ? 'créé' : 'modifié'));

            return $this->redirectToRoute('admin_event_show', ['slug' => $event->getSlug()]);
        }

        return $this->render('admin/event/edit.html.twig', [
            'creation' => $creation,
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
}
