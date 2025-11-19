<?php

declare(strict_types=1);

namespace App\Domain\DataTable;

final class AjaxDataSearch
{
    public function __construct(
        public string $value,
        public bool $regex,
    ) {}
}
