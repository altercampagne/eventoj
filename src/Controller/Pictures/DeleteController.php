<?php

declare(strict_types=1);

namespace App\Controller\Pictures;

use App\Entity\Document\EventPicture;
use App\Entity\User;
use App\Service\Media\ImageStorageManipulator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/pictures/{id}/delete', name: 'pictures_delete', requirements: ['id' => Requirement::UUID_V4], methods: 'POST')]
class DeleteController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ImageStorageManipulator $imageStorageManipulator,
    ) {
    }

    public function __invoke(
        EventPicture $picture,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        if ($user != $picture->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $event = $picture->getEvent();

        $this->imageStorageManipulator->delete($picture);

        $this->em->remove($picture);
        $this->em->flush();

        $this->addFlash('success', 'Photo supprimée.');

        return $this->redirectToRoute('pictures_upload', ['slug' => $event->getSlug()]);
    }
}
