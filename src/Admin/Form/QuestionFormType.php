<?php

declare(strict_types=1);

namespace App\Admin\Form;

use App\Entity\Question;
use App\Entity\QuestionCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Question>
 */
class QuestionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EnumType::class, [
                'class' => QuestionCategory::class,
                'label' => 'Catégorie de la question',
                'attr' => [
                    'placeholder' => 'Catégorie de la question',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('question', TextType::class, [
                'label' => 'La question',
                'attr' => [
                    'placeholder' => 'La question',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('answer', TrixType::class, [
                'label' => 'La réponse',
                'attr' => [
                    'placeholder' => 'La réponse',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
