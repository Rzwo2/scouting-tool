<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import;

use App\Domain\Statistic\Import\Model\FilterModel;
use App\Domain\Statistic\Import\Model\ImportModel;
use App\Domain\Statistic\Import\Model\ImportRequestModel;
use App\Domain\Statistic\Import\Model\ImportTypeModel;
use App\Domain\Statistic\Import\Model\PlayerStatsModel;
use App\Entity\PlayerGameStatistic;
use App\Repository\PlayerGameStatisticRepository;
use App\Repository\PlayerRepository;
use App\Serializer\OptionalCamelCaseToSnakeCaseConverter;
use App\Util\FlashMessageTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class StatisticImportService
{
    use FlashMessageTrait;

    public function __construct(
        #[Autowire(env: 'BALLTIME_TOKEN')]
        private string $token,
        private HttpClientInterface $client,
        private SerializerInterface $serializer,
        private PlayerRepository $playerRepository,
        private PlayerGameStatisticRepository $playerGameStatisticRepository,
        private EntityManagerInterface $entityManager,
        private DenormalizerInterface $objectNormalizer,
        private RequestStack $requestStack,
    ) {}

    public function handleImport(ImportTypeModel $importTypeModel): void
    {
        if (!($team = $importTypeModel->team) || !($game = $importTypeModel->game) || null === $importTypeModel->videoId) {
            throw new \UnexpectedValueException('Da stimmt was mit dem Formular nicht. Kontaktiere einen Admin');
        }

        /** @var array<string, PlayerGameStatistic> */
        $existingPlayerStats = array_column(
            array: $this->playerGameStatisticRepository->findImportIndexAndEntityByGameAndTeam($game, $team),
            column_key: 'playerGameStat',
            index_key: 'index',
        );

        $notFoundNumbers1 = $this->import($importTypeModel, $existingPlayerStats, isFirstBallSideOut: true);
        $notFoundNumbers2 = $this->import($importTypeModel, $existingPlayerStats, isFirstBallSideOut: false);

        $notFoundNumbers = array_merge($notFoundNumbers1, $notFoundNumbers2);

        $amountNew = count($this->entityManager->getUnitOfWork()->getScheduledEntityInsertions());
        $amountUpdated = count($this->entityManager->getUnitOfWork()->getScheduledEntityUpdates());

        $this->entityManager->flush();

        $successMessage = 'Import erfolgreich.';
        if ($amountNew) {
            $successMessage .= " $amountNew neue Daten importiert.";
        }
        if ($amountUpdated) {
            $successMessage .= "$amountUpdated Daten geupdated.";
        }

        $this->addSuccessMessage($successMessage);
        if ($notFoundNumbers) {
            $this->addWarningMessage('nicht gefundene Spieler-Nummern in Datenbank: ' . implode(' | ', $notFoundNumbers));
        }
    }

    /**
     * @param array<string, PlayerGameStatistic> $existingPlayerStats
     *
     * @return int[]
     */
    private function import(ImportTypeModel $importTypeModel, array $existingPlayerStats, bool $isFirstBallSideOut): array
    {
        (null !== ($team = $importTypeModel->team) && null !== ($game = $importTypeModel->game) && null !== ($videoId = $importTypeModel->videoId))
            ?: throw new \LogicException('Unmöglich');

        $response = $this->sendRequest($videoId, $isFirstBallSideOut);
        $playerStatsModels = $this->getPlayerStatsModelsFromResponse($response);

        $players = $this->playerRepository->findByTeamAndPlayerNumbersIndexedByNumber($team, array_column($playerStatsModels, 'jerseyNumber'));

        $notFoundNumbers = [];
        foreach ($playerStatsModels as $playerStatsModel) {
            if (null === $player = $players[$playerStatsModel->jerseyNumber] ?? null) {
                $notFoundNumbers[] = $playerStatsModel->jerseyNumber;
                continue;
            }

            $index = sprintf('%d|%d|%d', $player->getId(), $game->getId(), (int) $isFirstBallSideOut);

            $playerGameStatistic = $existingPlayerStats[$index] ?? new PlayerGameStatistic();
            $this->objectNormalizer->denormalize($playerStatsModel, PlayerGameStatistic::class, null, ['object_to_populate' => $playerGameStatistic]);

            if (!isset($existingPlayerStats[$index])) {
                $playerGameStatistic
                    ->setIsFirstBallSideOut($isFirstBallSideOut)
                    ->setPlayer($player)
                    ->setGame($game);

                $this->entityManager->persist($playerGameStatistic);
            }
        }

        return $notFoundNumbers;
    }

    private function sendRequest(string $videoId, bool $isFirstBallSideOut): ResponseInterface
    {
        $requestModel = new ImportRequestModel(
            videoIds: [$videoId],
            filters: new FilterModel(firstBallSideout: $isFirstBallSideOut),
        );
        $jsonBody = $this->serializer->serialize($requestModel, 'json', [OptionalCamelCaseToSnakeCaseConverter::CAMEL_CASE_TO_SNAKE_CASE => true]);

        return $this->client->request(
            method: 'POST',
            url: 'https://backend.balltime.com/generate-multi-video-stats?',
            options: [
                'auth_bearer' => $this->token,
                'body' => $jsonBody,
                'headers' => [
                    'Cache-Control' => 'no-cache',
                    'Content-Type' => 'application/json',
                    'Content-Length' => strlen($jsonBody),
                ],
            ],
        );
    }

    /** @return PlayerStatsModel[] */
    private function getPlayerStatsModelsFromResponse(ResponseInterface $response): array
    {
        /** @var ImportModel $model */
        $model = $this->serializer->deserialize(
            $response->getContent(),
            ImportModel::class,
            'json',
            [OptionalCamelCaseToSnakeCaseConverter::CAMEL_CASE_TO_SNAKE_CASE => true],
        );

        return array_filter($model->playerStats, static fn (PlayerStatsModel $stat) => null !== $stat->jerseyNumber);
    }
}
