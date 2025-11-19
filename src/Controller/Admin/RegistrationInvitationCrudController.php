<?php

namespace App\Controller\Admin;

use App\Domain\Registration\RegistrationService;
use App\Entity\RegistrationInvitation;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @extends AbstractCrudController<RegistrationInvitation> */
class RegistrationInvitationCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RegistrationService $tokenService,
        private readonly MailerInterface $mailer,
        private readonly UrlGeneratorInterface $url,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public static function getEntityFqcn(): string
    {
        return RegistrationInvitation::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addHtmlContentToHead('<style>.header-for-field-hidden, .field-hidden{display:none;}</style>')
            ->addHtmlContentToBody("<script>
                document.addEventListener('click', function(e){
                if(!e.target.closest('a')?.matches('[data-action-name=\"copyLink\"]')) return;
                const token = e.target.closest('tr').querySelector('td[data-column=\"token\"]').textContent.trim();
                const link = window.location.origin + '/register/' + token;
                try{
                    navigator.clipboard.writeText(link);
                } catch(error){
                console.log(error)
                }
                })
                </script>");
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('email', 'E-Mail')->setRequired(true);

        if (in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL])) {
            yield TextField::new('status', 'Status');
            yield TextField::new('registeredUser.username', 'Nutzer');
            yield DateTimeField::new('expiresAt', 'Gültig bis');
            yield HiddenField::new('token', 'Token');
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        $sendInvitation = Action::new('sendInvitation', 'Einladung erneut senden', 'fa fa-paper-plane')
            ->linkToCrudAction('sendInvitation')
            ->displayIf(function (RegistrationInvitation $invitation) {
                return $invitation->canSend();
            });

        $copyLink = Action::new('copyLink', 'Link kopieren', 'fa fa-copy')
            ->linkToCrudAction('copyLink')
            ->displayIf(function (RegistrationInvitation $invitation) {
                return !$invitation->isExpired() && !$invitation->getRegisteredUser();
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $sendInvitation)
            ->add(Crud::PAGE_INDEX, $copyLink)
            ->add(Crud::PAGE_DETAIL, $sendInvitation)
            ->add(Crud::PAGE_DETAIL, $copyLink)
            ->disable(Action::EDIT)
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($this->userRepository->count(['email' => $entityInstance->getEmail()])) {
            $this->addFlash('error', 'Nutzer mit dieser Email-Adresse existiert bereits');

            return;
        }

        $invitation = $this->tokenService->createOrUpdateInvitation($entityInstance->getEmail());
        if (!$invitation) {
            $this->addFlash('error', 'Einladung mit dieser Email wurde bereits erstellt');

            return;
        }
        try {
            $this->sendMail($invitation);
            $this->entityManager->flush();
            $this->addFlash('success', 'Einladung angelegt und versendet.');
        } catch (\Throwable $throwable) {
            $this->addFlash('error', "Email konnte nicht gesendet werden: {$throwable->getMessage()}");
        }
    }

    #[AdminRoute(path: '/send-invitation', name: 'send_invitation')]
    public function sendInvitation(AdminContext $context): Response
    {
        $invitation = $context->getEntity()->getInstance();

        if (!$invitation instanceof RegistrationInvitation) {
            throw new \LogicException('Entity must be an instance of RegistrationInvitation');
        }

        if (!$invitation->canSend()) {
            throw new \LogicException('Einladung kann nicht gesendet werden, da entweder Nutzer bereits registriert oder noch gültige Einladung erhalten');
        }

        $invitation = $this->tokenService->createOrUpdateInvitation($invitation->getEmail());

        try {
            $this->sendMail($invitation);
            $this->entityManager->flush();
            $this->addFlash('success', 'Einladung gesendet');
        } catch (\Throwable $throwable) {
            $this->addFlash('error', "Email konnte nicht gesendet werden: {$throwable->getMessage()}");
        }

        return $this->redirectToRoute('admin_registration_invitation_index');
    }

    public function copyLink(AdminContext $context): Response
    {
        $this->addFlash('success', 'Einladungslink wurde in Zwischenablage kopiert');

        return $this->redirectToRoute('admin_registration_invitation_index');
    }

    private function sendMail(RegistrationInvitation $invitation): void
    {
        $url = $this->url->generate('register', ['token' => $invitation->getEmail()], UrlGeneratorInterface::ABSOLUTE_URL);
        $email = (new TemplatedEmail())
            ->from('noreply@scouting-tool.de')
            ->to($invitation->getEmail())
            ->subject('Einladungslink Scouting Tool')
            ->htmlTemplate('emails/registration_invitation.html.twig')
            ->context([
                'appName' => 'VC Dresden Scouting Tool',
                'registrationUrl' => $url,
                'expiresAt' => $invitation->getExpiresAt(),
            ]);

        $this->mailer->send($email);
    }
}
