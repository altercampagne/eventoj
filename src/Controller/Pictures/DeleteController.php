<?php

declare(strict_types=1);

namespace App\Controller\Pictures;

use App\Entity\Document\EventPicture;
use App\Entity\User;
use Aws\S3\S3Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/pictures/{id}/delete', name: 'pictures_delete', requirements: ['id' => Requirement::UUID_V4], methods: 'POST')]
class DeleteController extends AbstractController
{
    public function __construct(
        private readonly S3Client $s3Client,
        private readonly EntityManagerInterface $em,
        #[Autowire(env: 'S3_BUCKET_NAME')]
        private readonly string $bucketName,
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

        $this->s3Client->deleteObject([
            'Bucket' => $this->bucketName,
            'Key' => $picture->getPath(),
        ]);

        $this->em->remove($picture);
        $this->em->flush();

        $this->addFlash('success', 'Photo supprimée.');

        return $this->redirectToRoute('pictures_upload', ['slug' => $event->getSlug()]);
    }
}
