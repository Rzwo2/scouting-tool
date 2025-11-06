<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\RegistrationInvitation;
use App\Entity\Team;
use App\Entity\User;
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
        yield MenuItem::linkToCrud('Benutzer', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Team', 'fas fa-list', Team::class);
        yield MenuItem::linkToCrud('Spieler', 'fas fa-list', Player::class);
        yield MenuItem::linkToCrud('Spiele', 'fas fa-list', Game::class);
        yield MenuItem::linkToCrud('Registrierung', 'fas fa-list', RegistrationInvitation::class);
    }
}
