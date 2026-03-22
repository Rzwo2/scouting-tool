<?php

namespace App\Controller;

use App\Domain\Registration\RegistrationService;
use App\Entity\User;
use App\Form\Security\ChangePasswordForm;
use App\Form\Security\ChangeUsernameForm;
use App\Form\Security\RegisterForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error || !$lastUsername) {
            return $this->render('security/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        }

        return $this->redirect('statistic');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register/{token}', name: 'app_register', requirements: ['token' => Requirement::CATCH_ALL])]
    public function register(
        string $token,
        Request $request,
        RegistrationService $registrationService,
    ): Response {
        if (null === ($registrationInvitation = $registrationService->validateToken($token))) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(RegisterForm::class);

        if ($request->isMethod(Request::METHOD_GET)) {
            return $this->render('security/register.html.twig', [
                'form' => $form,
            ]);
        }

        $form->handleRequest($request);
        /** @var User $user */
        $user = $form->getData();
        if ($user->getEmail() !== $registrationInvitation->getEmail()) {
            $form->get('email')->addError(new FormError(message: 'Email-Adresse stimmt nicht überein'));
        }

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('security/register.html.twig', [
                'form' => $form,
            ]);
        }

        $registrationService->registerUser($user, $registrationInvitation);

        return $this->redirect('app_login');
    }

    #[Route(path: '/profile', name: 'app_profile', methods: [Request::METHOD_GET])]
    public function profileAction(): Response
    {
        return $this->render('security/user-profile.html.twig', [
            'usernameForm' => $this->createForm(ChangeUsernameForm::class),
            'passwordForm' => $this->createForm(ChangePasswordForm::class),
        ]);
    }

    #[Route(path: '/profile/username', name: 'app_profile_change_username', methods: [Request::METHOD_POST])]
    public function changeUsernameAction(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangeUsernameForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();

            if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Das aktuelle Passwort ist falsch.');

                return $this->redirectToRoute('app_profile');
            }

            $user->setUsername($form->getData()['newUsername']);
            $this->entityManager->flush();
            $this->addFlash('success', 'Benutzername erfolgreich geändert.');
        } else {
            foreach ($form->getErrors(true) as $error) {
                assert($error instanceof FormError);
                $this->addFlash('error', $error->getMessage());
            }
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route(path: '/profile/password', name: 'app_profile_change_password', methods: [Request::METHOD_POST])]
    public function changePasswordAction(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Das aktuelle Passwort ist falsch.');

                return $this->redirectToRoute('app_profile');
            }

            $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
            $this->entityManager->flush();
            $this->addFlash('success', 'Passwort geändert. Bitte melde dich erneut an.');

            return $this->redirectToRoute('app_login');
        }

        foreach ($form->getErrors(true) as $error) {
            assert($error instanceof FormError);
            $this->addFlash('error', $error->getMessage());
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route(path: '/email', name: 'app_email', methods: ['GET'])]
    public function emailViewAction(): Response
    {
        return $this->render('emails/registration_invitation.html.twig', ['appName' => 'Scouting', 'registrationUrl' => 'test', 'expiresAt' => new \DateTimeImmutable()]);
    }
}
