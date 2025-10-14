<?php

namespace App\Controller\Admin;

use App\Entity\Player;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

/** @extends AbstractCrudController<Player> */
class PlayerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Player::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['team' => 'ASC', 'position' => 'ASC', 'number' => 'ASC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('team'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield NumberField::new('number', 'Nummer');
        yield TextField::new('firstName', 'Vorname');
        yield TextField::new('lastName', 'Nachname');
        yield NumberField::new('height', 'Größe')->formatValue(fn ($value) => "$value cm");
        yield DateField::new('birthDate', 'Geboren');
        yield TextField::new('position', 'Position');
        yield AssociationField::new('team');
    }
}
