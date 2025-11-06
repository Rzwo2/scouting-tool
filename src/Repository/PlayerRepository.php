<?php

namespace App\Repository;

use App\Entity\Player;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Player>
 */
class PlayerRepository extends ServiceEntityRepository
{
    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    /**
     * @param int[] $numbers
     *
     * @return array<int, Player>
     */
    public function findByTeamAndPlayerNumbersIndexedByNumber(Team $team, array $numbers): array
    {
        return $this->createQueryBuilder('player', 'player.number')
            ->where('player.number IN (:playerNumbers)')
            ->andWhere('player.team = :team')
            ->setParameter('playerNumbers', $numbers)
            ->setParameter('team', $team->getId())
            ->getQuery()
            ->getResult();
    }
}
