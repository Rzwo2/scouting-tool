<?php

declare(strict_types=1);

namespace App\Doctrine\Hydration;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class IndexedScalarHydrator extends AbstractHydrator
{
    public const string NAME = 'indexed_scalar';

    /** @return array<int|string, array<int, mixed>>|array<int|string, mixed> */
    protected function hydrateAllData(): array
    {
        $result = [];

        $index = $this->resultSetMapping()->indexByMap['scalars'] ?? null;
        while ($row = $this->statement()->fetchAssociative()) {
            $this->hydrateRow($row, $result, $index);
        }

        return $result;
    }

    /**
     * @param array<string, mixed>                                          $row
     * @param array<int|string, array<int, mixed>>|array<int|string, mixed> $result
     */
    protected function hydrateRow(array $row, array &$result, ?string $index): void
    {
        if (null === $index) {
            $result[] = $this->gatherScalarRowData($row);

            return;
        }

        $i = $row[$index] ?? null;
        unset($row[$index]);

        $result[$i] = 1 === count($row) ? reset($row) : $this->gatherScalarRowData($row);
    }
}
