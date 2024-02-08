<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class EventRegistrationPayFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('acceptCharter', CheckboxType::class, [
                'required' => true,
                'label' => false,
                'constraints' => [
                    new Assert\IsTrue(),
                ],
            ])
        ;
    }
}
