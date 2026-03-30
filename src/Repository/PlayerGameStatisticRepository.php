<?php

namespace App\Repository;

use App\Domain\Statistic\Overview\Model\StatisticModel;
use App\Entity\Game;
use App\Entity\PlayerGameStatistic;
use App\Entity\Team;
use App\Repository\Trait\PropertyFetchTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerGameStatistic>
 */
class PlayerGameStatisticRepository extends ServiceEntityRepository
{
    use PropertyFetchTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerGameStatistic::class);
    }

    /**
     * @return array<int, array{index: string, playerGameStat: PlayerGameStatistic}>
     */
    public function findImportIndexAndEntityByGameAndTeam(Game $game, Team $team): array
    {
        return $this->createQueryBuilder('stat')
            ->select(
                "CONCAT_WS('|', player.id, game.id, stat.isFirstBallSideOut) as index",
                'stat AS playerGameStat',
            )
            ->innerJoin('stat.game', 'game')
            ->innerJoin('stat.player', 'player')
            ->andWhere('game.id = :game')
            ->andWhere('player.team = :team')
            ->setParameter('game', $game->getId())
            ->setParameter('team', $team->getId())
            ->getQuery()->getResult();
    }

    /**
     * Returns the first PlayerGameStatistic found for each balltimeId, keyed by balltimeId.
     *
     * @param string[] $balltimeIds
     *
     * @return array<string, PlayerGameStatistic>
     */
    public function findByBalltimeIds(array $balltimeIds): array
    {
        if (!$balltimeIds) {
            return [];
        }

        return $this->createQueryBuilder('stat', 'stat.balltimeId')
            ->innerJoin('stat.game', 'game')
            ->innerJoin('game.teamOne', 'team1')
            ->innerJoin('game.teamTwo', 'team2')
            ->where('stat.balltimeId IN (:ids)')
            ->setParameter('ids', $balltimeIds)
            ->getQuery()
            ->getResult();
    }

    public function getQueryForStatisticDataByAjaxData(): QueryBuilder
    {
        $model = StatisticModel::class;

        return $this->createQueryBuilder('stat')
            ->select("NEW NAMED $model(
                team.name as team,
                CONCAT_WS(' : ', team1.name, team2.name) as game,
                player.number as number,
                player.position as position,
                CONCAT_WS(', ', player.lastName, player.firstName) as name,
                SUM(stat.serveAttempts) as serveAttempts,
                SUM(stat.serveAces) as serveAces,
                SUM(COALESCE(stat.serveAces,0) + COALESCE(stat.serve1s,0)) as serveSuccesss,
                SUM(stat.serveErrors) as serveErrors,
                SUM(stat.receiveAttempts) as receiveAttempts,
                SUM(stat.receive3s) as receive3s,
                SUM(stat.receive1s) as receive1s,
                SUM(stat.receive0s) as receive0s,
                SUM(stat.attackAttempts) as attackAttempts,
                SUM(stat.attackKills) as attackKills,
                SUM(IF(stat.isFirstBallSideOut = 1, stat.attackAttempts, 0)) as attackAttemptsK1,
                SUM(IF(stat.isFirstBallSideOut = 0, stat.attackAttempts, 0)) as attackAttemptsK2,
                SUM(IF(stat.isFirstBallSideOut = 1, stat.attackKills, 0)) as attackKillsK1,
                SUM(IF(stat.isFirstBallSideOut = 0, stat.attackKills, 0)) as attackKillsK2,
                SUM(stat.attackErrors) as attackErrors,
                SUM(stat.blockBlockSolos + stat.blockBlockAssists) as blockSuccesss
            )"
            )
            ->innerJoin('stat.player', 'player')
            ->innerJoin('player.team', 'team')
            ->innerJoin('stat.game', 'game')
            ->innerJoin('game.teamOne', 'team1')
            ->innerJoin('game.teamTwo', 'team2')
            ->groupBy('player.id');
    }
}
