<?php

declare(strict_types=1);

namespace App\Admin\Controller\Event;

use App\Admin\Security\Permission;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::EVENT_PUBLISH->value, 'event')]
#[Route('/events/{slug}/publish', name: 'admin_event_publish')]
class PublishController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request, Event $event): Response
    {
        if (0 === \count($event->getStages())) {
            $this->addFlash('danger', "L'évènement {$event->getName()} n'a aucune étape de définie pour le moment !");

            return $this->redirectToRightLocation($request, $event);
        }

        if (null == $event->getPicture()) {
            $this->addFlash('danger', "L'évènement {$event->getName()} n'a pas d'image d'illustration !");

            return $this->redirectToRightLocation($request, $event);
        }

        if (null == $event->getPahekoProjectId()) {
            $this->addFlash('danger', "L'évènement {$event->getName()} n'a pas d'ID de projet Paheko renseigné !");

            return $this->redirectToRightLocation($request, $event);
        }

        $event->setPublishedAt(new \DateTimeImmutable());

        $this->em->persist($event);
        $this->em->flush();

        $this->addFlash('success', "L'évènement {$event->getName()} est maintenant visible en ligne !");

        return $this->redirectToRightLocation($request, $event);
    }

    private function redirectToRightLocation(Request $request, Event $event): Response
    {
        if (null !== $targetUrl = $request->headers->get('Referer')) {
            return $this->redirect($targetUrl);
        }

        return $this->redirectToRoute('admin_event_show', ['slug' => $event->getSlug()]);
    }
}
