<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/** @extends AbstractCrudController<User> */
class UserCrudController extends AbstractCrudController
{
    public function __construct(private readonly RoleHierarchyInterface $roleHierarchy) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $user = $this->getUser();

        $roles = ($user instanceof User && !empty($user->getRoles()))
        ? $user->getRoles()
        : ['ROLE_USER'];

        $availableRoles = $this->roleHierarchy->getReachableRoleNames($roles);
        $roleChoices = array_combine($availableRoles, $availableRoles);

        yield TextField::new('username')->hideOnForm();
        yield EmailField::new('email')->hideOnForm();
        yield ArrayField::new('roles')->hideOnForm();
        yield ChoiceField::new('roles')
            ->setChoices($roleChoices)
            ->allowMultipleChoices()
            ->renderExpanded(false)
            ->onlyOnForms();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW);
    }
}
