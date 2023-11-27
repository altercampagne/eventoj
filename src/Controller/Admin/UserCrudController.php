<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Field\PhoneNumberField;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('utilisateurice')
            ->setEntityLabelInPlural('utilisateurices')
            ->setPageTitle('edit', fn (User $user) => "Modifier <b>{$user->getFullName()}</b>")
            ->setPageTitle('detail', fn (User $user) => "Utilisateurice <b>{$user->getFullName()}</b>")
            ->setSearchFields(['firstName', 'lastName', 'email', 'phoneNumber'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $email = EmailField::new('email');
        $phoneNumber = PhoneNumberField::new('phoneNumber', 'Numéro de téléphone');
        $roles = ArrayField::new('roles');
        $isVerified = BooleanField::new('isVerified', 'Mail vérifié')->renderAsSwitch(false);

        if (Crud::PAGE_INDEX === $pageName) {
            yield $email;
            yield TextField::new('fullName', 'Nom complet');
            yield $phoneNumber;
            yield $roles;
            yield $isVerified;

            return;
        }

        $firstName = TextField::new('firstName', 'Prénom');
        $lastName = TextField::new('lastName', 'Nom');

        if (Crud::PAGE_DETAIL === $pageName) {
            yield FormField::addFieldset('Informations personnelles')->setIcon('person');
            yield $firstName;
            yield $lastName;

            yield FormField::addFieldset('Informations de contact')->setIcon('phone');
            yield $email;
            yield $isVerified;
            yield $phoneNumber;
            yield TextField::new('address.addressLine1', 'Adresse');
            yield TextField::new('address.addressLine2', 'Adresse (complément)');
            yield TextField::new('address.zipCode', 'Code postal');
            yield TextField::new('address.city', 'Ville');
            yield TextField::new('address.countryCode', 'Code pays');
            yield TextareaField::new('biography')->renderAsHtml();

            yield FormField::addFieldset('Informations techniques')->setIcon('key');
            yield IdField::new('id');
            yield $roles;
            yield DateTimeField::new('createdAt', 'Date de création');

            return;
        }

        yield $email;
        yield $firstName;
        yield $lastName;
        yield $phoneNumber;
        yield $roles;
        yield $isVerified;
    }
}
