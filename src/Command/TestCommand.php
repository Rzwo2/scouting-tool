<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\DataTable\AjaxData;
use App\Domain\DataTable\AjaxDataSearch;
use App\Domain\Statistic\Import\StatisticImportService;
use App\Domain\Statistic\StatisticService;
use App\Repository\PlayerGameStatisticRepository;
use App\Repository\TeamRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test',
    description: 'Add a short description for your command',
)]
class TestCommand extends Command
{
    public function __construct(
        private readonly StatisticImportService $importService,
        private readonly TeamRepository $teamRepository,
        private readonly PlayerGameStatisticRepository $playerGameStatisticRepository,
        private readonly StatisticService $statisticService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $ajaxData = new AjaxData(1, 0, 20, [], [], new AjaxDataSearch('', false));

        $result = $this->statisticService->handleDataFetchRequest($ajaxData);

        var_dump($result);

        return Command::SUCCESS;
    }
}
