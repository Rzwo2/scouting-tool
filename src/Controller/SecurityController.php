<?php

namespace App\Controller;

use App\Domain\Registration\RegistrationService;
use App\Entity\User;
use App\Form\Security\RegisterForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
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

    #[Route(path: '/register/{token}', name: 'register', requirements: ['token' => Requirement::CATCH_ALL])]
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

        dd($form);
        $registrationService->registerUser($user, $registrationInvitation);

        return $this->redirect('app_login');
    }

    #[Route(path: '/profile', name: 'app_profile', methods: [Request::METHOD_GET])]
    public function profileAction(): Response
    {
        return $this->render('security/user-profile.html.twig');
    }
}
