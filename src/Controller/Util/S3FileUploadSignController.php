<?php

declare(strict_types=1);

namespace App\Controller\Util;

use App\Entity\Document\AbstractUploadedImage;
use App\Entity\Document\EventPicture;
use App\Entity\Document\UploadedImage;
use App\Entity\Document\UploadedImageType;
use App\Entity\Event;
use App\Entity\User;
use App\Service\UploadedImageUrlGenerator;
use Aws\S3\S3Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @see https://www.stijnvoss.com/chuncked-client-side-upload-s3-php-symfony.html
 * @see https://www.matsimitsu.com/blog/2021-04-20-remote-uploads-with-pre-signed-urls-on-scaleway-object-storage
 */
#[IsGranted('ROLE_USER')]
final class S3FileUploadSignController extends AbstractController
{
    public function __construct(
        private readonly S3Client $s3Client,
        private readonly EntityManagerInterface $em,
        private readonly UploadedImageUrlGenerator $uploadedImageUrlGenerator,
        #[Autowire(env: 'S3_BUCKET_NAME')]
        private readonly string $bucketName,
    ) {
    }

    #[Route('/s3_file_upload_sign/{type}/{prefix}', name: 's3_file_upload_sign', methods: ['POST'])]
    public function __invoke(
        Request $request,
        #[MapRequestPayload]
        S3FileUploadSignInput $input,
        UploadedImageType $type,
        string $prefix,
    ): Response {
        $randomPart = substr(bin2hex(random_bytes(5)), 0, 6);

        $uploadedImage = new UploadedImage($type, "{$type->value}/{$prefix}-{$randomPart}.{$input->getExt()}", $input->filename);

        return $this->sign($request, $uploadedImage, $input);
    }

    #[Route('/s3_file_upload_sign_user_upload_event/{event_slug}', name: 's3_file_upload_sign_user_upload_event', methods: ['POST'])]
    public function userUploadEvent(
        Request $request,
        #[MapRequestPayload]
        S3FileUploadSignInput $input,
        #[MapEntity(mapping: ['event_slug' => 'slug'])]
        Event $event,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $randomPart = substr(bin2hex(random_bytes(8)), 0, 15);

        $document = new EventPicture($user, $event, "user_upload/event/{$event->getSlug()}/{$user->getId()}/{$randomPart}.{$input->getExt()}", $input->filename);

        return $this->sign($request, $document, $input);
    }

    private function sign(
        Request $request,
        AbstractUploadedImage $document,
        S3FileUploadSignInput $input,
    ): Response {
        $document
            ->setSize($input->size)
            ->setMimeType($input->type)
            ->setWidth($input->width)
            ->setHeight($input->height)
        ;

        $command = $this->s3Client->getCommand('PutObject', [
            'Bucket' => $this->bucketName,
            'Key' => $document->getPath(),
            'ACL' => 'public-read',
            'Content-Type' => 'fileType',
        ]);
        $presignedUrl = (string) $this->s3Client->createPresignedRequest($command, '+20 minutes')->getUri();

        $this->em->persist($document);
        $this->em->flush();

        return $this->json([
            'uploaded_image_id' => (string) $document->getId(),
            'presigned_url' => $presignedUrl,
            'uploaded_image_url' => $this->uploadedImageUrlGenerator->getImageUrl($document, 'md'),
        ]);
    }
}
