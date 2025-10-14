<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

/** @extends AbstractCrudController<Game> */
class GameCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Game::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['date' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('teamOne', 'Team 1'),
            AssociationField::new('teamTwo', 'Team 2'),
            DateTimeField::new('date'),
        ];
    }
}
