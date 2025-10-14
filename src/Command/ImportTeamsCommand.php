<?php

namespace App\Command;

use App\Entity\Game;
use App\Entity\GameSet;
use App\Entity\Player;
use App\Entity\Team;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import',
    description: 'Add a short description for your command',
)]
class ImportTeamsCommand extends Command
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly TeamRepository $teamRepository,
        private readonly GameRepository $gameRepository,
        private readonly PlayerRepository $playerRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly NormalizerInterface $normalizer,
        private readonly SerializerInterface $serializer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $response = $this->client->request(
            'GET',
            'https://www.volleyball-bundesliga.de/cms/home/2_bundesliga_maenner/2_bundesliga_maenner_sued/mannschaften.xhtml',
        );

        $html = preg_replace('/<\?xml[^?]+\?>/', '', $response->getContent());

        $crawler = new Crawler($html);

        $linksCrawler = $crawler
            ->filter('form[action="/cms/home/2_bundesliga_maenner/2_bundesliga_maenner_sued/mannschaften.xhtml"] > div.samsCmsTeamListComponentBlock > a');

        $teamNames = $crawler
            ->filter('form h1.samsCmsComponentBlockHeader')
            ->each(static fn (Crawler $node) => $node->innerText());

        $teams = $this->fetchTeams($linksCrawler);
        $this->fetchGames($teams);

        $links = $linksCrawler
            ->each(static fn (Crawler $a) => $a->attr('href'));
        foreach ($links as $link) {
            preg_match('/teamId=(\d*)&/', $link, $matches);

            $team = $teams[$matches[1]];

            $this->fetchPlayers($link, $team);
        }

        $insertions = $this->entityManager->getUnitOfWork()->getScheduledEntityInsertions();
        $updates = $this->entityManager->getUnitOfWork()->getScheduledEntityUpdates();
        $deletions = $this->entityManager->getUnitOfWork()->getScheduledEntityDeletions();

        $data = ['teams' => [], 'players' => [], 'games' => 0, 'gameSets' => 0];
        $insertionsData = array_map(function ($entity) use ($data) {
            if ($entity instanceof Team) {
                $data['teams'][] = $entity->getName();
            } elseif ($entity instanceof Player) {
                $data['players'][] = $entity->__toString();
            } elseif ($entity instanceof Game) {
                ++$data['games'];
            } elseif ($entity instanceof GameSet) {
                ++$data['gameSets'];
            }
        }, $insertions);

        $a = [
            ['teams' => [], 'players' => [], 'games' => 0, 'gameSets' => 0],
        ];
        if ($data) {
            $io->table(array_keys($data), [[
                'teams' => implode("\n", array_column($data, 'teams')),
                'players' => implode("\n", array_column($data, 'players')),
                'games' => $data['games'],
                'gameSets' => $data['gameSets'],
            ]]);
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    /** @return array<string, Team> */
    private function fetchTeams(Crawler $linksCrawler): array
    {
        $dataList = [];
        $linksCrawler
            ->each(function (Crawler $linkNode) use (&$dataList) {
                $link = $linkNode->attr('href');
                preg_match('/teamId=(\d*)&/', $link, $matches);
                $teamId = $matches[1];
                $teamName = $linkNode->filter('h1.samsCmsComponentBlockHeader')->first()->innerText();
                $dataList[$teamId] = $teamName;
            });
        $teamIds = array_keys($dataList);

        $indexedExistingTeams = $this->teamRepository->createQueryBuilder('team', 'team.teamId')->getQuery()->getResult();

        $teams = [];
        foreach ($dataList as $teamId => $teamName) {
            if (null === $team = $indexedExistingTeams[$teamId] ?? null) {
                $team = new Team();
                $this->entityManager->persist($team);
            }
            $team
                ->setName($teamName)
                ->setTeamId($teamId);
            $teams[$teamId] = $team;
            unset($indexedExistingTeams[$teamId]);
        }

        if ($indexedExistingTeams) {
            $this->teamRepository->createQueryBuilder('team')->delete()->where('team.teamId IN (:teamIds)')->setParameter('teamId', array_keys($indexedExistingTeams))->getQuery()->execute();
        }

        return $teams;
    }

    private function fetchPlayers(string $link, Team $team): void
    {
        $response = $this->client->request(
            'GET',
            'https://www.volleyball-bundesliga.de' . $link,
        );

        $html = preg_replace('/<\?xml[^?]+\?>/', '', $response->getContent());

        $crawler = new Crawler($html);

        /** @var PlayerModel[] $playerModels */
        $playerModels = $crawler
            ->filter('[id$=teamPlayerTable] .samsDataTable tbody tr')
            ->each(function (Crawler $row) {
                [$name, $link] = $row->filter('td:nth-child(1)>a')->extract(['_text', 'href'])[0];
                preg_match('/teamMemberId=(\d*)&/', $link, $matches);
                $playerId = $matches[1];
                $height = $row->filter('td:nth-child(2)')->innerText();
                $birthDate = $row->filter('td:nth-child(3)')->innerText();
                $number = $row->filter('td:nth-child(4)')->innerText();
                $position = $row->filter('td:nth-child(6)>.hideLeSmall')->innerText();

                [$firstName, $lastName] = explode(',', $name);

                return new PlayerModel(
                    playerId: $playerId,
                    name: $name,
                    number: (int) $number,
                    height: (int) $height,
                    birthDate: $birthDate,
                    position: $position,
                );
            });

        $playerIds = array_column($playerModels, 'playerId');
        $indexedPlayers = $team->isNew()
            ? []
            : $this->playerRepository->createQueryBuilder('player', 'player.playerId')
                ->andWhere('player.team = :team')
                ->setParameter('team', $team->getId())
                ->getQuery()->getResult();

        foreach ($playerModels as $playerModel) {
            if (null === $player = $indexedPlayers[$playerModel->playerId] ?? null) {
                $player = new Player();
                $this->entityManager->persist($player);
            }

            [$lastName, $firstName] = explode(',', $playerModel->name);

            $player
                ->setPlayerId($playerModel->playerId)
                ->setFirstName(trim($firstName))
                ->setLastName(trim($lastName))
                ->setNumber($playerModel->number)
                ->setHeight((int) $playerModel->height)
                ->setBirthDate(new \DateTimeImmutable($playerModel->birthDate))
                ->setPosition($playerModel->position)
                ->setTeam($team);

            unset($indexedPlayers[$playerModel->playerId]);
        }

        if ($indexedPlayers) {
            $this->playerRepository->createQueryBuilder('player')->delete()
                ->where('player.playerId IN (:playerIds)')
                ->setParameter('playerIds', array_keys($indexedPlayers))
                ->getQuery()->execute();
        }
    }

    /** @param Team[] $teams */
    private function fetchGames(array $teams): void
    {
        $url = 'https://www.volleyball-bundesliga.de/servlet/league/PlayingScheduleCsvExport?matchSeriesId=776309863';

        $response = $this->client->request('GET', $url);

        $csv = mb_convert_encoding($response->getContent(), 'UTF-8', 'UTF-8, ISO-8859-1');

        /** @var GameFetchModel[] $dataList */
        $dataList = $this->serializer->deserialize($csv, GameFetchModel::class . '[]', 'csv', [CsvEncoder::DELIMITER_KEY => ';', CsvEncoder::ENCLOSURE_KEY]);

        $indexedTeams = array_combine(array_map(static fn (Team $team) => $team->getName(), $teams), $teams);
        $indexedGames = $this->gameRepository->createQueryBuilder('game', 'game.gameId')->leftJoin('game.gameSets', 'sets')->getQuery()->getResult();

        foreach ($dataList as $data) {
            if (!isset($indexedTeams[$data->team1], $indexedTeams[$data->team2])) {
                continue;
            }

            if (null === $game = $indexedGames[$data->gameId] ?? null) {
                $game = new Game();
                $this->entityManager->persist($game);
            }

            $game
                ->setGameId($data->gameId)
                ->setDate(new \DateTimeImmutable("$data->date $data->time"))
                ->setTeamOne($indexedTeams[$data->team1])
                ->setTeamTwo($indexedTeams[$data->team2])
            ;

            if (null !== $data->set1Points1) {
                $gameSet = $this->getGameSet($game, 1)
                    ->setPointsTeamOne($data->set1Points1)
                    ->setPointsTeamTwo($data->set1Points2)
                    ->setDurationMinutes($data->set1duration)
                ;
            }
            if (null !== $data->set2Points1) {
                $gameSet = $this->getGameSet($game, 2)
                    ->setPointsTeamOne($data->set2Points1)
                    ->setPointsTeamTwo($data->set2Points2)
                    ->setDurationMinutes($data->set2duration)
                ;
            }
            if (null !== $data->set3Points1) {
                $gameSet = $this->getGameSet($game, 3)
                    ->setPointsTeamOne($data->set3Points1)
                    ->setPointsTeamTwo($data->set3Points2)
                    ->setDurationMinutes($data->set3duration)
                ;
            }
            if (null !== $data->set4Points1) {
                $gameSet = $this->getGameSet($game, 4)
                    ->setPointsTeamOne($data->set4Points1)
                    ->setPointsTeamTwo($data->set4Points2)
                    ->setDurationMinutes($data->set4duration)
                ;
            }
            if (null !== $data->set5Points1) {
                $gameSet = $this->getGameSet($game, 5)
                    ->setPointsTeamOne($data->set5Points1)
                    ->setPointsTeamTwo($data->set5Points2)
                    ->setDurationMinutes($data->set5duration)
                ;
            }

            unset($indexedGames[$data->gameId]);
        }

        if ($indexedGames) {
            $this->gameRepository->createQueryBuilder('game')->delete()
                ->where('game.gameId IN (:gameIds)')
                ->setParameter('gameIds', array_keys($indexedGames))
                ->getQuery()->execute();
        }
    }

    private function getGameSet(Game $game, int $i): GameSet
    {
        $gameSets = $game->getGameSets();
        if (!$gameSet = $gameSets->findFirst(fn (int $i, GameSet $set) => 1 === $set->getSetNumber())) {
            $gameSet = (new GameSet())
                ->setSetNumber($i);
            $this->entityManager->persist($gameSet);
            $game->addGameSet($gameSet);
        }

        return $gameSet;
    }
}

class PlayerModel
{
    public function __construct(
        public string $playerId,
        public string $name,
        public string $height,
        public string $birthDate,
        public int $number,
        public string $position,
    ) {}
}

class GameFetchModel
{
    public \DateTime $dateTime;

    public function __construct(
        #[SerializedName('Datum')] public string $date,
        #[SerializedName('Uhrzeit')] public string $time,
        #[SerializedName('#')] public int $gameId,
        #[SerializedName('Mannschaft 1')] public string $team1,
        #[SerializedName('Mannschaft 2')] public string $team2,
        #[SerializedName('Satz 1 - Ballpunkte 1')] public ?int $set1Points1 = null,
        #[SerializedName('Satz 1 - Ballpunkte 2')] public ?int $set1Points2 = null,
        #[SerializedName('Satz 1 - Satzdauer')] public ?int $set1duration = null,
        #[SerializedName('Satz 2 - Ballpunkte 1')] public ?int $set2Points1 = null,
        #[SerializedName('Satz 2 - Ballpunkte 2')] public ?int $set2Points2 = null,
        #[SerializedName('Satz 2 - Satzdauer')] public ?int $set2duration = null,
        #[SerializedName('Satz 3 - Ballpunkte 1')] public ?int $set3Points1 = null,
        #[SerializedName('Satz 3 - Ballpunkte 2')] public ?int $set3Points2 = null,
        #[SerializedName('Satz 3 - Satzdauer')] public ?int $set3duration = null,
        #[SerializedName('Satz 4 - Ballpunkte 1')] public ?int $set4Points1 = null,
        #[SerializedName('Satz 4 - Ballpunkte 2')] public ?int $set4Points2 = null,
        #[SerializedName('Satz 4 - Satzdauer')] public ?int $set4duration = null,
        #[SerializedName('Satz 5 - Ballpunkte 1')] public ?int $set5Points1 = null,
        #[SerializedName('Satz 5 - Ballpunkte 2')] public ?int $set5Points2 = null,
        #[SerializedName('Satz 5 - Satzdauer')] public ?int $set5duration = null,
    ) {
        $this->dateTime = new \DateTime("$date $time");
    }
}
