<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import;

use App\Domain\Statistic\Import\Model\FilterModel;
use App\Domain\Statistic\Import\Model\ImportModel;
use App\Domain\Statistic\Import\Model\ImportRequestModel;
use App\Domain\Statistic\Import\Model\ImportTypeModel;
use App\Entity\PlayerGameStatistic;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class ImportService
{
    public function __construct(
        #[Autowire(env: 'BALLTIME_TOKEN')]
        private string $token,
        private HttpClientInterface $client,
        private SerializerInterface $serializer,
        private PlayerRepository $playerRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    public function handleImport(ImportTypeModel $importTypeModel): void
    {
        $requestModel = new ImportRequestModel(
            videoIds: [$importTypeModel->statisticId],
            filters: new FilterModel(firstBallSideout: false),
        );
        $jsonBody = $this->serializer->serialize($requestModel, 'json');
        $response = $this->client->request(
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

        /** @var ImportModel $model */
        $model = $this->serializer->deserialize($response->getContent(), ImportModel::class, 'json');

        $playerStats = $model->playerStats;
        dd($model->playerStats);

        /** @var PlayerGameStatistic[] $playerGameStatistics */
        $playerGameStatistics = array_filter($playerStats, static fn (PlayerGameStatistic $stat) => null !== $stat->jerseyNumber);

        $players = $this->playerRepository->createQueryBuilder('player', 'player.number')
            ->where('player.number IN (:playerNumbers)')
            ->andWhere('player.team = :team')
            ->setParameter('playerNumbers', array_column($playerGameStatistics, 'jerseyNumber'))
            ->setParameter('team', $importTypeModel->team->getId())
            ->getQuery()
            ->getResult();

        foreach ($playerGameStatistics as $playerGameStatistic) {
            if (null === $player = $players[$playerGameStatistic->jerseyNumber] ?? null) {
                // TODO: Add flash message if jerseynumber not exists
                continue;
            }

            $playerGameStatistic
                ->setPlayer($player)
                ->setGame($importTypeModel->game);

            $this->entityManager->persist();
        }
    }
}
