<?php

declare(strict_types=1);

namespace App\Controller\Util;

use App\Entity\UploadedFile;
use App\Entity\UploadedFileType;
use App\Service\UploadedFileUrlGenerator;
use Aws\S3\S3Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
#[Route('/s3_file_upload_sign/{type}/{prefix}', name: 's3_file_upload_sign', methods: ['POST'])]
final class S3FileUploadSignController extends AbstractController
{
    public function __construct(
        private readonly S3Client $s3Client,
        private readonly EntityManagerInterface $em,
        private readonly UploadedFileUrlGenerator $uploadedFileUrlGenerator,
        private readonly string $bucketName,
    ) {
    }

    public function __invoke(
        Request $request,
        UploadedFileType $type,
        string $prefix,
        #[MapRequestPayload]
        S3FileUploadSignInput $input,
    ): Response {
        $ext = pathinfo($input->filename, \PATHINFO_EXTENSION);
        if ('' == $ext) {
            $ext .= '.tmp';
        }

        $randomPart = substr(bin2hex(random_bytes(5)), 0, 6);
        $key = sprintf('%s/%s-%s.%s', $type->value, $prefix, $randomPart, $ext);

        $uploadedFile = new UploadedFile($type, $key, $input->filename);
        $uploadedFile
            ->setSize($input->size)
            ->setMimeType($input->type)
        ;

        $command = $this->s3Client->getCommand('PutObject', [
            'Bucket' => $this->bucketName,
            'Key' => $key,
            'ACL' => 'public-read',
            'Content-Type' => 'fileType',
        ]);
        $presignedUrl = (string) $this->s3Client->createPresignedRequest($command, '+20 minutes')->getUri();

        $this->em->persist($uploadedFile);
        $this->em->flush();

        return $this->json([
            'uploaded_file_id' => (string) $uploadedFile->getId(),
            'presigned_url' => $presignedUrl,
            'uploaded_file_url' => $this->uploadedFileUrlGenerator->getImageUrl($uploadedFile, 300, 300),
        ]);
    }
}
