<?php

declare(strict_types=1);

namespace App\Domain\DataTable;

final class AjaxDataColumn
{
    public function __construct(
        public string|int $data,
        public string $name,
        public bool $searchable,
        public bool $orderable,
        public AjaxDataSearch $search,
    ) {}
}
