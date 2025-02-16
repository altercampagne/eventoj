<?php

declare(strict_types=1);

namespace App\Entity\Document;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table(name: '`uploaded_image`')]
#[UniqueEntity(fields: ['path'], message: 'Il y a déjà un fichier avec ce chemin.')]
#[ORM\Index(name: 'idx_uploaded_file_path', fields: ['path'])]
class UploadedImage extends AbstractUploadedImage
{
    #[ORM\Column(type: 'string', enumType: UploadedImageType::class, options: [
        'comment' => 'Type of this file which correspond to the entity its linked to (event, alternative, ...).',
    ])]
    private readonly UploadedImageType $type;

    public function __construct(
        UploadedImageType $type,
        string $path,
        string $originalFileName,
        ?int $width = null,
        ?int $height = null,
    ) {
        parent::__construct($path, $originalFileName, $width, $height);

        $this->type = $type;
    }

    public function getType(): UploadedImageType
    {
        return $this->type;
    }
}
