<?php

declare(strict_types=1);

namespace App\Admin\Form\RegistrationCreate;

use App\Entity\Companion;
use App\Entity\Meal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<DetailsFormDTO>
 */
class DetailsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var DetailsFormDTO $dto */
        $dto = $builder->getData();

        $builder
            ->add('companions', EntityType::class, [
                'label' => 'Compagnon·e·s',
                'class' => Companion::class,
                'choices' => $dto->user->getCompanions(),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choice_label' => static fn (Companion $companion): string => $companion->getFullName(),
                'help' => $dto->user->getCompanions()->isEmpty() ? "{$dto->user->getPublicName()} n'a pas de compagnon·e pour le moment. Si besoin, demande-lui de les créer <b>AVANT</b> de procéder à son inscription." : null,
                'help_html' => true,
            ])
            ->add('bikes', IntegerType::class, [
                'label' => 'Nombre de vélos',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('start', DateType::class, [
                'label' => 'Jour d\'arrivée',
            ])
            ->add('firstMeal', EnumType::class, [
                'label' => 'Premier repas',
                'class' => Meal::class,
            ])
            ->add('end', DateType::class, [
                'label' => 'Jour de départ',
            ])
            ->add('lastMeal', EnumType::class, [
                'label' => 'Dernier repas',
                'class' => Meal::class,
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'currency' => false,
                'divisor' => 100,
                'scale' => 0,
                'label_attr' => ['class' => 'col-form-label'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DetailsFormDTO::class,
        ]);
    }
}
