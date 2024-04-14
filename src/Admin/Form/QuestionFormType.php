<?php

declare(strict_types=1);

namespace App\Admin\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Question $question */
        $question = $options['data'];

        $builder
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
