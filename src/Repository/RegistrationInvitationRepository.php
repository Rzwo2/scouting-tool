<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RegistrationInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepositoryProxy<RegistrationInvitation> */
class RegistrationInvitationRepository extends ServiceEntityRepositoryProxy
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
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()->getOneOrNullResult();
    }
}
