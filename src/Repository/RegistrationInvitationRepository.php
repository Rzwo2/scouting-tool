<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RegistrationInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<RegistrationInvitation> */
class RegistrationInvitationRepository extends ServiceEntityRepository
{
    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistrationInvitation::class);
    }

    public function findByNotExpiredToken(string $token): ?RegistrationInvitation
    {
        return $this->createQueryBuilder('invitation')
            ->andWhere('invitation.token = :token')
            ->andWhere('invitation.expiresAt > :now')
            ->andWhere('invitation.registeredUser IS NULL')
            ->setParameter('token', $token)
            ->setParameter('now', new \DateTimeImmutable(), Types::DATETIME_IMMUTABLE)
            ->getQuery()->getOneOrNullResult();
    }
}
