<?php

declare(strict_types=1);

namespace App\Admin\Form;

use App\Entity\Event;
use App\Entity\UploadedFileType as UploadedFileTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Event $event */
        $event = $options['data'];

        $builder
            ->add('picture', UploadedFileType::class, [
                'required' => false,
                'type' => UploadedFileTypeEnum::EVENT,
                'prefix' => sprintf('%s-%s', $event->getType()->value, $event->getId()),
                'label' => false,
                'help' => 'Merci de choisir une image <b>carrée</b> et de dimensions respectables (600 x 600 minimum). Ce n\'est pas grave si l\'image est un peu lourde, ce sera automagiquement optimisé ! 👌',
                'help_html' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'évènement',
                'attr' => [
                    'placeholder' => 'Nom de l\'évènement',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => 'Choisir quelque de simple et court type "AlterTour 2042", "Échappée Belle féministe 2031", ...',
            ])
            ->add('description', TrixType::class, [
                'label' => 'Description de l\'évènement',
                'attr' => [
                    'placeholder' => 'Description de l\'évènement',
                    'style' => 'height: 100px',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => 'Ne pas indiquer de dates ou de jauge de capacité, c\'est inutile ! :)',
            ])
            ->add('openingDateForBookings', DateTimeType::class, [
                'label' => 'Ouverture des réservations',
                'attr' => [
                    'placeholder' => 'Ouverture des réservations',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'disabled' => $event->isBookable(),
            ])
            ->add('adultsCapacity', IntegerType::class, [
                'label' => 'Jauge adultes',
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'disabled' => $event->isBookable(),
            ])
            ->add('childrenCapacity', IntegerType::class, [
                'label' => 'Jauge enfants',
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'disabled' => $event->isBookable(),
            ])
            ->add('bikesAvailable', IntegerType::class, [
                'label' => 'Vélos de prêt',
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'disabled' => $event->isBookable(),
            ])
            ->add('pahekoProjectId', IntegerType::class, [
                'required' => false,
                'label' => 'ID du projet Paheko',
                'attr' => [
                    'placeholder' => 'ID du projet Paheko',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => '<i class="fa-solid fa-triangle-exclamation"></i> Tu ne sais pas ce que c\'est ? Clique sur "Plus d\'infos". 😉',
                'help_html' => true,
            ])
            ->add('breakEvenPricePerDay', MoneyType::class, [
                'currency' => false,
                'label' => 'Prix d\'équilibre (en euros)',
                'attr' => [
                    'placeholder' => 'Prix d\'équilibre (en euros)',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => 'C\'est le prix qui sera suggéré par défaut lors de l\'inscription à l\'évènement',
                'divisor' => 100,
                'scale' => 0,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
