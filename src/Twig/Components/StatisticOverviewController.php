<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Repository\GameRepository;
use App\Repository\TeamRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/statistic/overview.html.twig')]
class StatisticOverviewController extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $team = null;

    #[LiveProp(writable: true)]
    public ?string $game = null;

    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly GameRepository $gameRepository,
        private readonly LoggerInterface $logger,
    ) {}

    /** @return array<int, array{id: int, name:string}> */
    public function getTeamsData(): array
    {
        $qb = $this->teamRepository->createQueryBuilder('team')
            ->distinct()
            ->select('team.name as name', 'team.id as id')
            ->innerJoin('team.players', 'player')
            ->innerJoin('player.playerGameStatistics', 'stat')
            ->innerJoin('stat.game', 'game')
        ;
        if ($this->game) {
            $this->logger->critical('GAME: ' . $this->game);
            $teams = explode(' : ', $this->game);
            $qb->where('team.name IN (:teams)')
                ->setParameter('teams', $teams);
        }

        return $qb->getQuery()->getScalarResult();
    }

    /** @return array<int, array{id: int, name:string}> */
    public function getGamesData(): array
    {
        $qb = $this->gameRepository->createQueryBuilder('game')
            ->distinct()
            ->select("CONCAT_WS(' : ', team1.name, team2.name) as name", 'game.id as id')
            ->innerJoin('game.teamOne', 'team1')
            ->innerJoin('game.teamTwo', 'team2')
            ->innerJoin('game.playerGameStatistics', 'stats')
        ;

        if ($this->team) {
            $qb->where('team1.name = :team OR team2.name = :team')
                ->setParameter('team', $this->team);
        }

        return $qb->getQuery()->getScalarResult();
    }
}
