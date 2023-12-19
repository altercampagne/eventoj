<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Address;
use App\Entity\Alternative;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AlternativeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Alternative::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('une alternative')
            ->setEntityLabelInPlural('alternatives')
            ->setPageTitle('edit', fn (Alternative $alternative) => "Modifier <b>{$alternative->getName()}</b>")
            ->setPageTitle('new', 'Ajouter une nouvelle alternative')
            ->setPageTitle('detail', fn (Alternative $alternative) => "Alternative <b>{$alternative->getName()}</b>")
            ->setSearchFields(['name', 'description'])
        ;
    }

    public function createEntity(string $entityFqcn): Alternative
    {
        return (new Alternative())->setAddress(new Address());
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();
        yield TextField::new('name', 'Nom');

        if (Crud::PAGE_EDIT === $pageName || Crud::PAGE_NEW === $pageName) {
            yield TextEditorField::new('description')->setHelp('Merci de faire une mise en forme simple (pas de titres, de listes, de citations, ...)');
            yield TextField::new('address.addressLine1', 'Adresse');
            yield TextField::new('address.addressLine2', 'Complément d\'adresse');
            yield TextField::new('address.zipCode', 'Code postal');
            yield TextField::new('address.city', 'Ville');
            yield CountryField::new('address.countryCode', 'Pays')->includeOnly(['FR', 'BE']);
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            yield TextareaField::new('description')->renderAsHtml();
            yield TextField::new('address.addressLine1', 'Adresse');
            yield TextField::new('address.addressLine2', 'Complément d\'adresse');
            yield TextField::new('address.zipCode', 'Code postal');
            yield TextField::new('address.city', 'Ville');
            yield CountryField::new('address.countryCode', 'Pays');
            yield DateTimeField::new('createdAt', 'Date de création');
        }
    }
}
