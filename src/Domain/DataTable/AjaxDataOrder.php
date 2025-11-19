<?php

declare(strict_types=1);

namespace App\Domain\DataTable;

final class AjaxDataOrder
{
    public function __construct(
        public int $column,
        public string $dir,
    ) {}
}
