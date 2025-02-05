<?php

declare(strict_types=1);

namespace App\Admin\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

/**
 * @extends AbstractType<User>
 */
#[AsEntityAutocompleteField]
class UserAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => User::class,
            'searchable_fields' => ['firstName', 'lastName', 'publicName', 'email'],
            'choice_label' => static function (User $user): string {
                return \sprintf('%s (%s)', $user->getFullName(), $user->getPublicName());
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
