<?php

declare(strict_types=1);

namespace App\Domain\DataTable;

class DataTablesFilterModel
{
    public function __construct(
        public int $draw,
        public array $columns,
        public array $order,
        public int $start,
        public int $length,
        public array $search,
    ) {}
}
