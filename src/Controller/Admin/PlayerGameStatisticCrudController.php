<?php

namespace App\Controller\Admin;

use App\Entity\PlayerGameStatistic;
use App\Form\Statistic\Import\StatisticImportType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\HttpFoundation\Response;

/** @extends AbstractCrudController<PlayerGameStatistic> */
class PlayerGameStatisticCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PlayerGameStatistic::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            NumberField::new('player.number', 'Nummer'),
            AssociationField::new('player', 'Spieler'),
            AssociationField::new('game', 'Spiel'),
            BooleanField::new('isFirstBallSideOut', 'K1')->renderAsSwitch(false)->hideValueWhenFalse(),
            NumberField::new('totalPoints', 'Ges')->onlyOnIndex(),
            NumberField::new('totalWinMinusLose', 'W-L')->onlyOnIndex(),
            NumberField::new('serveAttempts', 'Serv-Ges'),
            NumberField::new('serveSuccesss', 'Pkt ImS'),
            PercentField::new('serveSuccesssPercent', 'Pkt ImS')->setNumDecimals(2),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('game'))
            ->add(EntityFilter::new('player'))
            ->add(EntityFilter::new('isFirstBallSideOut'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $importAction = Action::new('importStatistic', 'Import')
            /* ->renderAsButton('button') */
            ->asPrimaryAction()
            ->linkToCrudAction('renderImport')
            ->createAsGlobalAction()
        ;

        return $actions
            ->remove(Crud::PAGE_INDEX, 'edit')
            ->add(Crud::PAGE_INDEX, $importAction);
    }

    public function renderImport(): Response
    {
        return $this->render('statistic/import/index.html.twig', ['formData' => null, 'form' => $this->createForm(StatisticImportType::class)]);
    }
}
