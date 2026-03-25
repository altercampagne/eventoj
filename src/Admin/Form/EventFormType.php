<?php

declare(strict_types=1);

namespace App\Admin\Form;

use App\Entity\Document\UploadedImageType as UploadedImageTypeEnum;
use App\Entity\Event;
use App\Entity\Meal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Event>
 */
class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Event $event */
        $event = $options['data'];

        $builder
            ->add('picture', UploadedImageType::class, [
                'required' => false,
                'type' => UploadedImageTypeEnum::EVENT,
                'prefix' => \sprintf('%s-%s', $event->getType()->value, $event->getId()),
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
                'help' => "⚠️  Une fois que l'évènement a été ouvert, il n'est plus possible de modifier cette date !",
                'help_attr' => [
                    'class' => 'text-warning-emphasis',
                ],
                'disabled' => $event->isBookable(),
            ])
            ->add('adultsCapacity', IntegerType::class, [
                'label' => 'Jauge adultes',
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('childrenCapacity', IntegerType::class, [
                'label' => 'Jauge enfants',
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('bikesAvailable', IntegerType::class, [
                'label' => 'Vélos de prêt',
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
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
            ->add('minimumPricePerDay', MoneyType::class, [
                'currency' => false,
                'label' => 'Prix minimum',
                'attr' => [
                    'placeholder' => 'Prix minimum',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'divisor' => 100,
                'scale' => 0,
            ])
            ->add('daysAtSolidarityPrice', IntegerType::class, [
                'label' => 'Nombre de jours maximum au tarif solidaire',
                'attr' => [
                    'placeholder' => 'Nombre de jours maximum au tarif solidaire',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => 'Si une personne reste plus que ce nombre de jours, le prix des jours suivants sera automatiquement fixé au prix d\'équilibre',
            ])
            ->add('breakEvenPricePerDay', MoneyType::class, [
                'currency' => false,
                'label' => 'Prix d\'équilibre',
                'attr' => [
                    'placeholder' => 'Prix d\'équilibre',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'divisor' => 100,
                'scale' => 0,
            ])
            ->add('supportPricePerDay', MoneyType::class, [
                'currency' => false,
                'label' => 'Prix de soutien',
                'attr' => [
                    'placeholder' => 'Prix de soutien',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'divisor' => 100,
                'scale' => 0,
            ])
            ->add('firstMealOfFirstDay', EnumType::class, [
                'class' => Meal::class,
                'label' => 'Premier repas du premier jour',
                'attr' => [
                    'placeholder' => 'Premier repas du premier jour',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('lastMealOfLastDay', EnumType::class, [
                'class' => Meal::class,
                'label' => 'Dernier repas du dernier jour',
                'attr' => [
                    'placeholder' => 'Dernier repas du dernier jour',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('exchangeMarketLink', UrlType::class, [
                'required' => false,
                'label' => 'URL de la bourse aux échanges',
                'attr' => [
                    'placeholder' => 'URL de la bourse aux échanges',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'help' => 'Généralement un Google doc pour permettre aux personnes qui ne viennent plus d\'échanger / de revendre leurs places. Si aucun fichier n\'est renseigné, l\'évènement pourra tout de même être publié mais la bourse aux échanges ne sera pas suggérée sur le site.',
                'default_protocol' => null,
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
