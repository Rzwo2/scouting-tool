<?php

declare(strict_types=1);

namespace App\Domain\DataTable;

final class AjaxResponse
{
    public function __construct(
        public object $data,
        public ?int $draw = null,
        public ?int $recordsTotal = null,
        public ?int $recordsFiltered = null,
        public ?string $error = null,
    ) {}
}
