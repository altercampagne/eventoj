<?php

declare(strict_types=1);

namespace App\Form\EventRegistration;

use App\Twig\Runtime\PriceExtensionRuntime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @extends AbstractType<array{price: int}>
 */
class ChoosePriceFormType extends AbstractType
{
    public function __construct(
        private readonly PriceExtensionRuntime $priceExtension,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var int $minimumPrice */
        $minimumPrice = $options['minimum_price'];

        $builder
            ->add('price', MoneyType::class, [
                'currency' => false,
                'label' => false,
                'attr' => [
                    'size' => 3,
                    'class' => 'border border-primary text-primary fw-bold',
                    'min' => $minimumPrice,
                ],
                'divisor' => 100,
                'scale' => 0,
                'constraints' => [
                    new Assert\NotBlank(
                        message: "L'acceptation de la charte n'est pas optionnelle !",
                    ),
                    new Assert\GreaterThanOrEqual(
                        value: $minimumPrice,
                        message: 'Le prix minimum est de '.$this->priceExtension->formatPrice($minimumPrice).'.',
                    ),
                ],
            ])
            ->add('acceptCharter', CheckboxType::class, [
                'mapped' => false,
                'required' => true,
                'label' => false,
                'constraints' => [
                    new Assert\IsTrue(
                        message: "L'acceptation de la charte n'est pas optionnelle !",
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['minimum_price']);
        $resolver->setAllowedTypes('minimum_price', 'int');
    }
}
