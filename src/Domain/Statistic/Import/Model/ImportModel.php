<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import\Model;

use App\Entity\PlayerGameStatistic;

readonly class ImportModel
{
    /**
     * @param PlayerGameStatistic[] $playerStats
     */
    public function __construct(
        public array $playerStats,
    ) {}
}
