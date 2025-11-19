<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Overview\Model;

class StatisticModel
{
    public ?int $pointsTotal = null;
    public ?int $pointsDiff = null;
    public ?float $serveSuccesssPercent = null;
    public ?float $serveErrorsPercent = null;
    public ?float $receive3sPercent = null;
    public ?float $receive1sPercent = null;
    public ?float $receive0sPercent = null;
    public ?float $attackKillsK1Percent = null;
    public ?float $attackKillsK2Percent = null;
    public ?float $attackKillsPercent = null;
    public ?float $attackErrorsPercent = null;

    public function __construct(
        public string $team,
        public string $game,
        public int $number,
        public string $position,
        public string $name,
        public ?int $serveAttempts = null,
        public ?int $serveSuccesss = null,
        public ?int $serveErrors = null,
        public ?int $receiveAttempts = null,
        public ?int $receive3s = null,
        public ?int $receive1s = null,
        public ?int $receive0s = null,
        public ?int $attackAttempts = null,
        public ?int $attackKills = null,
        public ?int $attackAttemptsK1 = null,
        public ?int $attackAttemptsK2 = null,
        public ?int $attackKillsK1 = null,
        public ?int $attackKillsK2 = null,
        public ?int $attackErrors = null,
        public ?int $blockSuccesss = null,
        private ?int $serveAces = null,
    ) {
        $this->pointsTotal = $serveAces + $attackKills + $blockSuccesss;
        $this->pointsDiff = $this->pointsTotal - $serveErrors - $receive0s - $attackErrors;
        $this->serveSuccesssPercent = $this->percent($serveSuccesss, $serveAttempts);
        $this->serveErrorsPercent = $this->percent($serveErrors, $serveAttempts);
        $this->receive3sPercent = $this->percent($receive3s, $receiveAttempts);
        $this->receive1sPercent = $this->percent($receive1s, $receiveAttempts);
        $this->receive0sPercent = $this->percent($receive0s, $receiveAttempts);
        $this->attackKillsK1Percent = $this->percent($attackKillsK1, $attackAttemptsK1);
        $this->attackKillsK2Percent = $this->percent($attackKillsK2, $attackAttemptsK2);
        $this->attackKillsPercent = $this->percent($attackKills, $attackAttempts);
        $this->attackErrorsPercent = $this->percent($attackErrors, $attackAttempts);
    }

    private function percent(?int $part, ?int $total): ?float
    {
        if (!$total || !$part) {
            return null;
        }

        return round(($part * 100) / $total, 0);
    }
}
