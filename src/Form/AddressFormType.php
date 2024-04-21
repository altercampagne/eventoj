<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class, [
                'label' => 'Ton adresse',
                'attr' => [
                    'placeholder' => 'Ton adresse',
                    'class' => 'address-autocomplete',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3 address-autocomplete-container',
                ],
                'mapped' => false,
            ])
            ->add('countryCode', HiddenType::class, [
                'label' => false,
            ])
            ->add('addressLine1', HiddenType::class, [
                'label' => false,
                'required' => $options['is_address_line_required'],
                'constraints' => $options['is_address_line_required'] ? [new NotBlank(message: 'Ton adresse doit inclure une rue et un numéro de rue.')] : [],
            ])
            ->add('addressLine2', HiddenType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('zipCode', HiddenType::class, [
                'label' => false,
            ])
            ->add('city', HiddenType::class, [
                'label' => false,
            ])
            ->add('latitude', HiddenType::class, [
                'label' => false,
            ])
            ->add('longitude', HiddenType::class, [
                'label' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, static function (PostSetDataEvent $event): void {
            /** @var Address $address */
            $address = $event->getData();

            $event->getForm()->get('address')->setData((string) $address);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'is_address_line_required' => true,
            'error_mapping' => [
                '.' => 'address',
                'addressLine1' => 'address',
                'addressLine2' => 'address',
                'zipCode' => 'address',
                'city' => 'address',
                'latitude' => 'address',
                'longitude' => 'address',
            ],
        ]);
        $resolver->setAllowedTypes('is_address_line_required', 'bool');
    }
}
