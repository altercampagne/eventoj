<?php

declare(strict_types=1);

namespace App\Admin\Form;

use App\Admin\Form\DataTransformer\RouteUrlTransformer;
use App\Entity\Stage;
use App\Entity\StageDifficulty;
use App\Entity\StageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Stage>
 */
class StageFormType extends AbstractType
{
    public function __construct(
        private readonly RouteUrlTransformer $routeUrlTransformer,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Stage $stage */
        $stage = $options['data'];

        $builder
            ->add('type', EnumType::class, [
                'class' => StageType::class,
                'label' => 'Type d\'étape',
                'attr' => [
                    'placeholder' => 'Type d\'étape',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('date', DateType::class, [
                'label' => 'Date de l\'étape',
                'attr' => [
                    'placeholder' => 'Nom de l\'étape',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'étape',
                'attr' => [
                    'placeholder' => 'Nom de l\'étape',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('description', TrixType::class, [
                'label' => 'Programme de l\'étape',
                'attr' => [
                    'placeholder' => 'Programme de l\'étape',
                    'style' => 'height: 200px',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => 'Ne pas répéter la date ou la difficulté, ce n\'est pas nécessaire.',
            ])
            ->add('difficulty', EnumType::class, [
                'required' => false,
                'class' => StageDifficulty::class,
                'label' => 'Difficulté de l\'étape',
                'attr' => [
                    'placeholder' => 'Difficulté de l\'étape',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('routeUrl', TextType::class, [
                'required' => false,
                'label' => 'URL de l\'itinéraire',
                'attr' => [
                    'placeholder' => 'URL de l\'itinéraire',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('alternatives', AlternativeAutocompleteField::class, [
                'required' => false,
                'label' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control-lg',
                    'placeholder' => 'Rechercher',
                ],
                'help' => 'Indique ici toutes les alternatives traversées ou visitées dans la journée. :)',
            ])
            ->add('preparers', UserAutocompleteField::class, [
                'label' => false,
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control-lg',
                    'placeholder' => 'Rechercher',
                ],
            ])
        ;

        $builder->get('routeUrl')->addModelTransformer($this->routeUrlTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stage::class,
        ]);
    }
}
