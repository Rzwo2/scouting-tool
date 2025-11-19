<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RegistrationInvitationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RegistrationInvitationRepository::class)]
class RegistrationInvitation
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 180)]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(unique: true)]
    private string $token;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $expiresAt;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'registrationInvitation')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'cascade')]
    private ?User $registeredUser = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getRegisteredUser(): ?User
    {
        return $this->registeredUser;
    }

    public function setRegisteredUser(?User $registeredUser): static
    {
        $this->registeredUser = $registeredUser;

        return $this;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function canSend(): bool
    {
        return !$this->registeredUser && $this->isExpired();
    }

    public function getStatus(): string
    {
        if ($this->registeredUser) {
            return 'registriert';
        }
        if ($this->isExpired()) {
            return 'abgelaufen';
        }

        return 'ausstehend';
    }
}
