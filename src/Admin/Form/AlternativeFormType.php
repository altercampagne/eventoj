<?php

declare(strict_types=1);

namespace App\Admin\Form;

use App\Entity\Alternative;
use App\Entity\AlternativeCategory;
use App\Entity\UploadedFileType as UploadedFileTypeEnum;
use App\Form\AddressFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Alternative>
 */
class AlternativeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Alternative $alternative */
        $alternative = $options['data'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'alternative',
                'attr' => [
                    'placeholder' => 'Nom de l\'alternative',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('website', TextType::class, [
                'required' => false,
                'label' => 'Site internet',
                'attr' => [
                    'placeholder' => 'Site internet',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('description', TrixType::class, [
                'label' => 'Description de l\'alternative',
                'attr' => [
                    'placeholder' => 'Description de l\'alternative',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => '<b>Ne surtout pas</b> indiquer d\'informations spécifiques à ton étape (lieu de rencontre, date, ...) dans cette description, ce n\'est pas prévu pour ! 🙏',
                'help_html' => true,
            ])
            ->add('categories', EnumType::class, [
                'class' => AlternativeCategory::class,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('address', AddressFormType::class, [
                'address_line1_required' => false,
            ])
            ->add('pictures', CollectionType::class, [
                'entry_type' => UploadedFileType::class,
                'entry_options' => [
                    'required' => false,
                    'type' => UploadedFileTypeEnum::ALTERNATIVE,
                    'prefix' => (string) $alternative->getId(),
                    'label' => false,
                    'help' => 'Merci de choisir une image <b>au format paysage 4/3</b> (c\'est le format standard) et de bonne qualité (640 x 480 minimum). Ce n\'est pas grave si l\'image est un peu lourde, ce sera automagiquement optimisé ! 👌',
                    'help_html' => true,
                    'preview_width' => 300,
                    'preview_height' => 225,
                    'allow_delete' => true,
                ],
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('stations', CollectionType::class, [
                'entry_type' => StationFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false, // Needed to force the Form component to call the `setStations`method.
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alternative::class,
        ]);
    }
}
