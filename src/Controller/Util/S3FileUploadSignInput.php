<?php

declare(strict_types=1);

namespace App\Controller\Util;

use Symfony\Component\Validator\Constraints as Assert;

final class S3FileUploadSignInput
{
    #[Assert\NotBlank(message: 'Le nom du fichier doit être fourni !')]
    public string $filename;

    #[Assert\Type('integer')]
    public ?int $size = null;

    public ?string $type = null;
}
