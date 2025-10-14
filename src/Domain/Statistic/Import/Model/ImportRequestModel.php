<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import\Model;

class ImportRequestModel
{
    /** @param string[] $videoIds */
    public function __construct(
        public array $videoIds,
        public FilterModel $filters = new FilterModel(),
    ) {}
}
