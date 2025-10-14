<?php

declare(strict_types=1);

namespace App\Domain\DataTable;

/** @template T of object */
class DataTableResponseModel
{
    /** @param T $data */
    public function __construct(
        public $data,
        public ?int $recordsFiltered = null,
        public ?int $recordsTotal = null,
        public ?int $draw = null,
        public ?string $error = null,
    ) {}
}
