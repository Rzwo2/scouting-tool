<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import\Model;

use App\Entity\Game;
use App\Entity\Team;

class ImportTypeModel
{
    public function __construct(
        public Team $team,
        public Game $game,
        public string $statisticId,
    ) {}
}
