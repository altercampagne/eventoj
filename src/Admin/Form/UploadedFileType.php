<?php

declare(strict_types=1);

namespace App\Admin\Form;

use App\Entity\UploadedFile;
use App\Entity\UploadedFileType as UploadedFileTypeEnum;
use App\Service\UploadedFileUrlGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UploadedFileType extends AbstractType
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly EntityManagerInterface $em,
        private readonly UploadedFileUrlGenerator $uploadedFileUrlGenerator,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('type');
        $resolver->setAllowedTypes('type', UploadedFileTypeEnum::class);
        $resolver->setRequired('prefix');
        $resolver->setAllowedTypes('prefix', 'string');

        $resolver->setDefaults([
            'attr' => [
                'class' => 'aws-file-upload d-none',
            ],
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addModelTransformer(new CallbackTransformer(
                static function (?UploadedFile $uploadedFile): ?string {
                    if (null === $uploadedFile) {
                        return null;
                    }

                    return (string) $uploadedFile->getId();
                },
                function (?string $id): ?UploadedFile {
                    if (null === $id) {
                        return null;
                    }

                    return $this->em->find(UploadedFile::class, $id);
                }
            ))
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var UploadedFileTypeEnum $type */
        $type = $options['type'];

        $view->vars['attr']['data-sign-url'] = $this->urlGenerator->generate('s3_file_upload_sign', [
            'type' => $type->value,
            'prefix' => $options['prefix'],
        ]);

        $file = $form->getData();

        if ($file instanceof UploadedFile) {
            $view->vars['fileUrl'] = $this->uploadedFileUrlGenerator->getImageUrl($file, 300, 300);
        } else {
            $view->vars['fileUrl'] = 'https://placehold.co/300?text=Choisir une\nimage';
        }
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
