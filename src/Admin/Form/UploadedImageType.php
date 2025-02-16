<?php

declare(strict_types=1);

namespace App\Admin\Form;

use App\Entity\Document\UploadedImage;
use App\Entity\Document\UploadedImageType as UploadedImageTypeEnum;
use App\Service\UploadedImageUrlGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @extends AbstractType<string>
 */
class UploadedImageType extends AbstractType
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly EntityManagerInterface $em,
        private readonly UploadedImageUrlGenerator $uploadedImageUrlGenerator,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('type');
        $resolver->setAllowedTypes('type', UploadedImageTypeEnum::class);
        $resolver->setRequired('prefix');
        $resolver->setAllowedTypes('prefix', 'string');
        $resolver->setRequired('preview_width');
        $resolver->setAllowedTypes('preview_width', 'int');
        $resolver->setRequired('preview_height');
        $resolver->setAllowedTypes('preview_height', 'int');

        $resolver->setDefaults([
            'attr' => [
                'class' => 'aws-file-upload d-none',
            ],
            'preview_width' => 300,
            'preview_height' => 300,
            'allow_delete' => false,
        ]);

        $resolver->setAllowedTypes('allow_delete', 'bool');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addModelTransformer(new CallbackTransformer(
                static function (?UploadedImage $uploadedImage): ?string {
                    if (null === $uploadedImage) {
                        return null;
                    }

                    return (string) $uploadedImage->getId();
                },
                function (?string $id): ?UploadedImage {
                    if (null === $id) {
                        return null;
                    }

                    return $this->em->find(UploadedImage::class, $id);
                }
            ))
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var UploadedImageTypeEnum $type */
        $type = $options['type'];
        /** @var int $width */
        $width = $options['preview_width'];
        /** @var int $height */
        $height = $options['preview_height'];

        /* @phpstan-ignore offsetAccess.nonOffsetAccessible */
        $view->vars['attr']['data-sign-url'] = $this->urlGenerator->generate('s3_file_upload_sign', [
            'type' => $type->value,
            'prefix' => $options['prefix'],
            'width' => $width,
            'height' => $height,
        ]);

        /** @var ?UploadedImage $file */
        $file = $form->getData();

        $view->vars['fileUrl'] = $this->uploadedImageUrlGenerator->getImageUrl($file, width: $width, height: $height);
        $view->vars['preview_width'] = $width;
        $view->vars['preview_height'] = $height;

        $view->vars['allow_delete'] = $options['allow_delete'];
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
