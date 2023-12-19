<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('un évènement')
            ->setEntityLabelInPlural('évènements ')
            ->setPageTitle('edit', fn (Event $event) => "Modifier <b>{$event->getName()}</b>")
            ->setPageTitle('new', 'Ajouter un nouvel évènement')
            ->setPageTitle('detail', fn (Event $event) => "Evènement <b>{$event->getName()}</b>")
            ->setSearchFields(['name', 'description'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();
        yield TextField::new('name', 'Nom');

        if (Crud::PAGE_EDIT === $pageName || Crud::PAGE_NEW === $pageName) {
            yield TextEditorField::new('description')->setHelp('Merci de faire une mise en forme simple (pas de titres, de listes, de citations, ...)');
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            yield TextareaField::new('description')->renderAsHtml();
            yield DateTimeField::new('createdAt', 'Date de création');
        }
    }
}
