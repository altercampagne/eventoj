<?php

declare(strict_types=1);

namespace App\Form\EventRegistration;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ChoosePriceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pricePerDay', IntegerType::class, [
                'attr' => [
                    'min' => 20,
                    'class' => 'form-control-lg',
                ],
            ])
            ->add('acceptCharter', CheckboxType::class, [
                'mapped' => false,
                'required' => true,
                'label' => false,
                'constraints' => [
                    new Assert\IsTrue(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventRegistrationDTO::class,
        ]);
    }
}
