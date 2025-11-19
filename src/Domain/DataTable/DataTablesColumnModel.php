<?php

declare(strict_types=1);

namespace App\Domain\DataTable;

class DataTablesColumnModel
{
    public function __construct(
        public string|int $data,
        public string $name,
        public bool $searchable,
        public bool $orderable,
        public array $search,
    ) {}
}
