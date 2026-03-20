<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Domain\Statistic\Import\Model\Balltime\BalltimeVideosResponseDto;
use App\Domain\Statistic\Import\Model\ImportTypeModel;
use App\Domain\Statistic\Import\StatisticImportService;
use App\Repository\GameRepository;
use App\Repository\PlayerGameStatisticRepository;
use App\Repository\TeamRepository;
use App\Serializer\OptionalCamelCaseToSnakeCaseConverter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/statistic/bulk-import-wizard.html.twig')]
#[IsGranted('ROLE_ADMIN')]
class BulkImportWizard extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp]
    public string $step = 'idle';

    #[LiveProp]
    public array $folders = [];

    #[LiveProp(writable: true)]
    public string $selectedFolderId = '';

    #[LiveProp]
    public array $allVideos = [];

    #[LiveProp]
    public array $conflicts = [];

    #[LiveProp]
    public int $currentConflictIndex = 0;

    #[LiveProp(writable: true)]
    public array $videoMappings = [];

    #[LiveProp]
    public ?string $errorMessage = null;

    public function __construct(
        #[Autowire(env: 'BALLTIME_TOKEN')]
        private readonly string $token,
        private readonly HttpClientInterface $client,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly TeamRepository $teamRepository,
        private readonly GameRepository $gameRepository,
        private readonly PlayerGameStatisticRepository $playerGameStatisticRepository,
        private readonly StatisticImportService $statisticImportService,
        private readonly LoggerInterface $logger,
    ) {}

    /** @return array<int, array{id: int, name: string}> */
    public function getAllTeams(): array
    {
        return $this->teamRepository->createQueryBuilder('team')
            ->select('team.id as id', 'team.name as name')
            ->orderBy('team.name')
            ->getQuery()
            ->getScalarResult();
    }

    /**
     * @return array<int, array<int, array{id: int, name: string}>>
     */
    public function getGamesByTeamId(): array
    {
        $rows = $this->gameRepository->createQueryBuilder('game')
            ->select('game.id as id', 'team1.id as team1Id', 'team1.name as team1Name', 'team2.id as team2Id', 'team2.name as team2Name', 'game.date as date')
            ->innerJoin('game.teamOne', 'team1')
            ->innerJoin('game.teamTwo', 'team2')
            ->orderBy('game.date')
            ->getQuery()
            ->getScalarResult();

        $result = [];
        foreach ($rows as $row) {
            $game = ['id' => $row['id'], 'name' => $row['team1Name'] . ' : ' . $row['team2Name']];
            $result[$row['team1Id']][] = $game;
            $result[$row['team2Id']][] = $game;
        }

        return $result;
    }

    #[LiveAction]
    public function loadVideos(): void
    {
        try {
            $response = $this->client->request('GET', 'https://backend.balltime.com/library/videos', [
                'auth_bearer' => $this->token,
            ]);

            /** @var LibraryResponseDto $dto */
            $dto = $this->serializer->deserialize(
                $response->getContent(),
                BalltimeVideosResponseDto::class,
                'json',
                [OptionalCamelCaseToSnakeCaseConverter::CAMEL_CASE_TO_SNAKE_CASE => true],
            );

            $violations = $this->validator->validate($dto);
            if (count($violations) > 0) {
                $this->errorMessage = 'Ungültige Antwort von Balltime API.';

                return;
            }
            $this->folders = array_column($dto->folders, 'name', 'id');
            $this->allVideos = [];
            foreach ($dto->videos as $video) {
                $this->allVideos[$video->id] = ['title' => $video->title, 'folderId' => $video->folderId];
            }

            $this->step = 'folder_select';
        } catch (\Throwable $e) {
            $this->logger->error('BulkImportWizard::loadVideos failed', ['exception' => $e]);
            $this->errorMessage = 'Fehler beim Laden der Videobibliothek. Bitte versuche es erneut.';
        }
    }

    #[LiveAction]
    public function confirmFolderSelection(): void
    {
        if ('' === $this->selectedFolderId) {
            return;
        }

        /** @var array<string, array{title: string, folderId: ?string}> $folderVideos */
        $folderVideos = array_filter($this->allVideos, fn ($v) => $v['folderId'] === $this->selectedFolderId);

        $existingStats = $this->playerGameStatisticRepository->findByBalltimeIds(array_keys($folderVideos));

        $this->conflicts = [];
        $this->videoMappings = [];
        foreach ($folderVideos as $videoId => $video) {
            if (!isset($existingStats[$videoId])) {
                $this->videoMappings[] = [
                    'videoId' => $videoId,
                    'title' => $video['title'],
                    'teamId' => '',
                    'gameId' => '',
                ];

                continue;
            }

            $stat = $existingStats[$videoId];
            $game = $stat->getGame();
            $this->conflicts[] = [
                'videoId' => $videoId,
                'title' => $video['title'],
                'entryDescription' => $game->getTeamOne()->getName() . ' : ' . $game->getTeamTwo()->getName()
                    . ' (' . $game->getDate()?->format('d.m.Y') . ')',
                'teamId' => '',
                'gameId' => '',
            ];
        }

        $this->currentConflictIndex = 0;
        $this->step = count($this->conflicts) > 0 ? 'conflict_resolve' : 'video_mapping';
    }

    #[LiveAction]
    public function resolveConflict(#[LiveArg] string $decision): void
    {
        if (!isset($this->conflicts[$this->currentConflictIndex])) {
            $this->step = 'video_mapping';

            return;
        }

        if ('yes_all' === $decision || 'no_all' === $decision) {
            if ('yes_all' === $decision) {
                for ($i = $this->currentConflictIndex; $i < count($this->conflicts); ++$i) {
                    $conflict = $this->conflicts[$i];
                    $this->videoMappings[] = [
                        'videoId' => $conflict['videoId'],
                        'title' => $conflict['title'],
                        'teamId' => $conflict['teamId'],
                        'gameId' => $conflict['gameId'],
                    ];
                }
            }
            $this->step = 'video_mapping';

            return;
        }

        $conflict = $this->conflicts[$this->currentConflictIndex];

        if ('yes' === $decision) {
            $this->videoMappings[] = [
                'videoId' => $conflict['videoId'],
                'title' => $conflict['title'],
                'teamId' => $conflict['teamId'],
                'gameId' => $conflict['gameId'],
            ];
        }

        ++$this->currentConflictIndex;

        if ($this->currentConflictIndex >= count($this->conflicts)) {
            $this->step = 'video_mapping';
        }
    }

    #[LiveAction]
    public function executeImport(): ?Response
    {
        foreach ($this->videoMappings as $i => $mapping) {
            if ('' === ($mapping['teamId'] ?? '')) {
                unset($this->videoMappings[$i]);
                continue;
            }
            if ('' === ($mapping['gameId'] ?? '')) {
                $this->errorMessage = 'Bitte Team und Spiel für alle Videos auswählen.';

                return null;
            }
        }

        $importItems = [];
        foreach ($this->videoMappings as $mapping) {
            $team = $this->teamRepository->find((int) $mapping['teamId']);
            $game = $this->gameRepository->find((int) $mapping['gameId']);
            if (!$team || !$game) {
                $this->errorMessage = "Team oder Spiel nicht gefunden.\nTEAM: " . $mapping['teamId'];

                return null;
            }
            $importItems[] = new ImportTypeModel($team, $game, $mapping['videoId']);
        }

        try {
            $this->statisticImportService->handleBulkImport($importItems);
        } catch (\Throwable $e) {
            $this->logger->error('BulkImportWizard::executeImport failed', ['exception' => $e]);
            $this->addFlash('error', 'Beim Bulk-Import ist ein Fehler aufgetreten. Kontaktiere den Admin.');
        }

        return $this->redirectToRoute('statistic_overview');
    }
}
