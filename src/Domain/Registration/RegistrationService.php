<?php

declare(strict_types=1);

namespace App\Domain\Registration;

use App\Entity\RegistrationInvitation;
use App\Entity\User;
use App\Repository\RegistrationInvitationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RegistrationInvitationRepository $invitationRepository,
        private UserRepository $userRepository,
        private UrlGeneratorInterface $url,
    ) {}

    public function validateToken(string $token): ?RegistrationInvitation
    {
        return $this->invitationRepository->findByNotExpiredToken($token);
    }

    public function canCreateInvitation(string $email): bool
    {
        return (bool) $this->userRepository->count(['email' => $email]);
    }

    public function createOrUpdateInvitation(string $email): ?RegistrationInvitation
    {
        $invitation = $this->invitationRepository->findOneBy(['email' => $email]);
        if (!$invitation->isExpired()) {
            return null;
        }

        if (!$invitation) {
            $invitation = (new RegistrationInvitation())
                ->setEmail($email);
            $this->entityManager->persist($invitation);
        }

        $invitation
            ->setToken($this->generateToken())
            ->setExpiresAt(new \DateTimeImmutable('+24 hours'));

        return $invitation;
    }

    public function getRegistrationUrl(string $token): string
    {
        return $this->url->generate('register', ['token' => $token]);
    }

    public function registerUser(User $user, RegistrationInvitation $invitation): void
    {
        $user->setRegistrationInvitation($invitation);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
