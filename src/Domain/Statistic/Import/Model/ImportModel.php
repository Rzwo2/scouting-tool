<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import\Model;

readonly class ImportModel
{
    /**
     * @param PlayerStatsModel[] $playerStats
     */
    public function __construct(
        public array $playerStats,
    ) {}
}
