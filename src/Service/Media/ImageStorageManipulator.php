<?php

declare(strict_types=1);

namespace App\Service\Media;

use App\Entity\Document\AbstractUploadedImage;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class ImageStorageManipulator
{
    public function __construct(
        private S3Client $s3Client,
        #[Autowire(env: 'S3_BUCKET_NAME')]
        private string $bucketName,
    ) {
    }

    public function exists(AbstractUploadedImage $image): bool
    {
        try {
            $object = $this->s3Client->getObject([
                'Bucket' => $this->bucketName,
                'Key' => $image->getPath(),
            ]);
        } catch (S3Exception $e) {
            if (null === $previous = $e->getPrevious()) {
                throw $e;
            }

            if (404 === $previous->getCode()) {
                return false;
            }

            throw $e;
        }

        return true;
    }

    public function delete(AbstractUploadedImage $image): void
    {
        $this->s3Client->deleteObject([
            'Bucket' => $this->bucketName,
            'Key' => $image->getPath(),
        ]);
    }

    public function getPath(AbstractUploadedImage $image): string
    {
        return (string) $this->s3Client->getObjectUrl($this->bucketName, $image->getPath());
    }
}
