<?php

namespace App\Domain\DvvDataImport\MessageHandler;

use App\Domain\DvvDataImport\Message\PlayerDataImportMessage;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
final readonly class PlayerDataImportMessageHandler
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private HttpClientInterface $client,
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(PlayerDataImportMessage $message): void
    {
        $players = $this->playerRepository->findAll();

        $images = [];
        foreach ($players as $player) {
            $playerId = $player->getPlayerId();
            $response = $this->client->request(
                'GET',
                "https://www.volleyball-bundesliga.de/popup/teamMember/teamMemberDetails.xhtml?teamMemberId=$playerId",
            );

            $html = preg_replace('/<\?xml[^?]+\?>/', '', $response->getContent());

            $crawler = new Crawler($html);

            $linkCrawler = $crawler
                ->filter('img')
                ->reduce(static fn (Crawler $el) => !$el->attr('id'))
                ->first()
                ->extract(['src']);
            $player->setPictureLink($linkCrawler[0] ?? null);
        }

        $this->entityManager->flush();
    }
}
