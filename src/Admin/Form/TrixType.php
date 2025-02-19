<?php

declare(strict_types=1);

namespace App\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<string>
 */
class TrixType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    #[\Override]
    public function getParent(): string
    {
        return HiddenType::class;
    }
}
