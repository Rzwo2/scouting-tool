<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->redirectToRoute('admin_team_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Scouting Tool');
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setDateFormat('dd.MM.yyyy')
            ->setTimeFormat('HH:mm')
            ->setDateTimeFormat('dd.MM.yy HH:mm')
            ->hideNullValues()
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Statistik', 'fas fa-chart-bar', '/statistic');
        yield MenuItem::linkTo(RegistrationInvitationCrudController::class, 'Registrierung', 'fas fa-list');
        yield MenuItem::linkTo(UserCrudController::class, 'Benutzer', 'fas fa-list');
        yield MenuItem::linkTo(TeamCrudController::class, 'Team', 'fas fa-list');
        yield MenuItem::linkTo(PlayerCrudController::class, 'Spieler', 'fas fa-list');
        yield MenuItem::linkTo(GameCrudController::class, 'Spiele', 'fas fa-list');
    }
}
