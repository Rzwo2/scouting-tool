<?php

declare(strict_types=1);

namespace App\Domain\DataTable;

final class AjaxData
{
    /**
     * @param AjaxDataOrder[]  $order
     * @param AjaxDataColumn[] $columns
     */
    public function __construct(
        public int $draw,
        public int $start,
        public int $length,
        public array $columns,
        public AjaxDataSearch $search,
        public ?array $order = null,
        public mixed $data = null,
    ) {}

    public function getColumnSearchValue(string $columnName): ?string
    {
        foreach ($this->columns as $column) {
            if ($column->data === $columnName && $column->search?->value) {
                return $column->search->value;
            }
        }

        return null;
    }

    public function hasColumnSearch(string $columnName): bool
    {
        return null !== $this->getColumnSearchValue($columnName);
    }
}
