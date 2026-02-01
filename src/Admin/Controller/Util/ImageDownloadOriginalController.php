<?php

declare(strict_types=1);

namespace App\Admin\Controller\Util;

use App\Admin\Security\Permission;
use App\Entity\Document\UploadedImage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::IMAGE_DOWNLOAD_ORIGINAL->value)]
#[Route('/_admin/image/{id}/Download', name: 'admin_image_download_original')]
class ImageDownloadOriginalController extends AbstractController
{
    public function __construct(
        #[Autowire(env: 'S3_ENDPOINT')]
        private readonly string $s3Endpoint,
        #[Autowire(env: 'S3_BUCKET_NAME')]
        private readonly string $s3BucketName,
    ) {
    }

    /**
     * @see https://medium.com/@a.marakhin2077/creating-a-file-download-in-symfony-5-from-remote-url-d2a51b1cf547
     */
    public function __invoke(UploadedImage $image): Response
    {
        $url = $this->s3Endpoint.'/'.$this->s3BucketName.'/'.$image->getPath();

        $response = new StreamedResponse(static function () use ($url): void {
            if (false === $stream = fopen($url, 'r')) {
                throw new \RuntimeException('Unable to open remote file.');
            }

            // Dasplaying file data by parts
            while (!feof($stream)) {
                echo fread($stream, 1024); // Reading by 1KB from file
                flush(); // Forcing output to buffer
            }

            fclose($stream); // Closing the stream
        });

        // Setting the response headers
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$image->getOriginalFileName()}\"");

        return $response;
    }
}
