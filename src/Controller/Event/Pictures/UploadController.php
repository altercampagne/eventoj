<?php

declare(strict_types=1);

namespace App\Controller\Event\Pictures;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\Document\EventPictureRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/event/{slug}/pictures/upload', name: 'event_pictures_upload')]
class UploadController extends AbstractController
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly EventPictureRepository $eventPictureRepository,
    ) {
    }

    public function __invoke(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Event $event,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('event/pictures/upload.html.twig', [
            'event' => $event,
            'pictures' => $this->eventPictureRepository->findByUserAndEvent($user, $event),
            'uploadSignUrl' => $this->urlGenerator->generate('s3_file_upload_sign_user_upload_event', [
                'event_slug' => $event->getSlug(),
            ]),
        ]);
    }
}
