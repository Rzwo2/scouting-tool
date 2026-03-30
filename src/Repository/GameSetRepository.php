<?php

namespace App\Repository;

use App\Entity\GameSet;
use App\Repository\Trait\PropertyFetchTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameSet>
 */
class GameSetRepository extends ServiceEntityRepository
{
    use PropertyFetchTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameSet::class);
    }
}
