<?php

namespace App\Command;

use App\Entity\Player;
use App\Entity\Team;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import-teams',
    description: 'Add a short description for your command',
)]
class ImportTeamsCommand extends Command
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly TeamRepository $teamRepository,
        private readonly PlayerRepository $playerRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {
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

        $teamNames = $crawler
            ->filter('form h0.samsCmsComponentBlockHeader')
            ->each(static fn(Crawler $node)=>$node->innerText());

        $this->createTeams($teamNames, $io);

        $links = $crawler->filter('form[action="/cms/home/2_bundesliga_maenner/2_bundesliga_maenner_sued/mannschaften.xhtml"] .samsCmsTeamListComponentBlock>a')
            ->each(static fn(Crawler $a) => $a->attr('href'));
        foreach($links as $link){
            $this->createPlayers($link, $io);
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    /** @param string[] $teamNames */
    private function createTeams(array $teamNames, SymfonyStyle $io): void
    {
        $existingTeamNames = array_column($this->teamRepository->findPropertiesBy(['name'], ['name'=>$teamNames]), 'name');
        $newTeamNames = array_diff($teamNames, $existingTeamNames);

        foreach($newTeamNames as $teamName){
            $team = (new Team())
                ->setName($teamName);

            $this->entityManager->persist($team);

            $io->success("created new Team '$teamName'");
        }

        $this->entityManager->flush();

        if (!empty($newTeamNames)){
            $io->success("Teams created: " . implode("\n", $newTeamNames));
        }

        if (!empty($existingTeamNames)){
            $io->warning("Teams already exist: " . implode("\n", $existingTeamNames));
        }
    }

    private function createPlayers(string $link, SymfonyStyle $io): void
    {
        $response = $this->client->request(
            'GET',
            'https://www.volleyball-bundesliga.de' . $link,
        );

        $html = preg_replace('/<\?xml[^?]+\?>/', '', $response->getContent());

        $crawler = new Crawler($html);

        $teamName = $crawler->filter('h1.samsCmsComponentBlockHeader')->innerText();
        $team = $this->teamRepository->findOneBy(['name'=>$teamName]);
        if (!$team)
        {
            $team = (new Team())
                ->setName($teamName);

            $this->entityManager->persist($team);
        }

        /** @var array<int, array{number: int, name:string}> $player*/
        $playersData = $crawler
            ->filter('[id$=teamPlayerTable] .samsDataTable tbody tr')
            ->each(function(Crawler $row) use ($team){
                $name = $row->filter('td:nth-child(1)>a')->innerText();
                $number = (int)$row->filter('td:nth-child(4)')->innerText();

                return ['number'=>$number, 'name'=>$name];
            });

        $this->playerRepository->createQueryBuilder('player')
            ->delete()
            ->andWhere('player.team = :team')
            ->andWhere('player.number NOT IN (:numbers)')
            ->setParameter('team', $team->getId())
            ->setParameter('numbers', array_column($playersData, 'number'))
            ->getQuery()->execute();

        $existingPlayers = $this->playerRepository->findBy(['team'=>$team->getId()]);
        $numberIndexedExistingPlayers = array_map(static fn(Player $player)=>[$player->getNumber() => $player], $existingPlayers);

        foreach($playersData as $playerData){
            ['number' => $number, 'name' => $name] = $playerData;
            $player = $numberIndexedExistingPlayers[$number] ?? new Player();
            $player
                ->setNumber($number)
                ->setName($name)
                ->setTeam($team);

            $this->entityManager->persist($player);
        }
    }
}

