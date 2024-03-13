<?php

declare(strict_types=1);

namespace App\Admin\Form;

use App\Entity\Station;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => ['Train' => 'train', 'Bus' => 'bus'],
                'label' => 'Type de gare',
                'attr' => [
                    'placeholder' => 'Type de gare',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom de la gare',
                'attr' => [
                    'placeholder' => 'Nom de la gare',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('distance', IntegerType::class, [
                'label' => 'Distance (kms)',
                'attr' => [
                    'placeholder' => 'Distance (kms)',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Station::class,
            'empty_data' => function (FormInterface $form): Station {
                /** @var string $type */
                $type = $form->get('type')->getData();
                /** @var string $name */
                $name = $form->get('name')->getData();
                /** @var int $distance */
                $distance = $form->get('distance')->getData();

                return new Station($type, $name, $distance);
            },
        ]);
    }
}
