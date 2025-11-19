<?php

declare(strict_types=1);

namespace App\Domain\DataTable;

use Doctrine\ORM\Query\Expr\Select;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

final class DataTablesHelper
{
    public static function applyDataTablesAjaxData(QueryBuilder $qb, AjaxData $ajaxData): void
    {
        self::addFilters($qb, $ajaxData);
        self::addOrdering($qb, $ajaxData);
        $qb->setFirstResult($ajaxData->start)->setMaxResults($ajaxData->length);
    }

    public static function addFilters(QueryBuilder $qb, AjaxData $ajaxData): void
    {
        $selectParts = self::getSelectParts($qb);
        foreach ($ajaxData->columns as $column) {
            if (!$column->searchable || !($searchValue = $column->search->value)) {
                continue;
            }

            $property = $column->data ?? $column->name
                ?? throw new MissingOptionsException('One of data or name must be defined in the column');

            $selectPart = $selectParts[$property]
                ?? throw new \UnexpectedValueException("No Select expression found for column '$property'.");

            $parameter = "search_$property";

            if (is_numeric($searchValue)) {
                $qb->andWhere("$selectPart = :$parameter");
            } elseif (is_array($searchValue)) {
                $qb->andWhere("$selectPart IN (:$parameter)");
            } else {
                $qb->andWhere("$selectPart LIKE :$parameter");
            }
            $qb->setParameter($parameter, "%$searchValue%");
        }
    }

    public static function addOrdering(QueryBuilder $qb, AjaxData $ajaxData): void
    {
        if (!$ajaxData->order) {
            return;
        }

        $selectParts = self::getSelectParts($qb);
        foreach ($ajaxData->order as $order) {
            $column = $ajaxData->columns[$order->column];
            $name = $column->data ?? $column->name
                ?? throw new MissingOptionsException('One of data or name must be defined in the column');

            $field = $selectParts[$name];

            $qb->addOrderBy($field, strtoupper($order->dir));
        }
    }

    /** call after filters are applied */
    public static function getAmountFiltered(QueryBuilder $qb): int
    {
        $countQb = clone $qb;

        $count = $countQb->getDQLPart('groupBy')[0]?->getParts()[0] ?? 1;

        return (int) $countQb
            ->resetDQLPart('orderBy')
            ->resetDQLPart('groupBy')
            ->select("COUNT(DISTINCT $count)")
            ->setFirstResult(0)
            ->setMaxResults(null)
            ->getQuery()->getSingleScalarResult();
    }

    /** call before filters are applied */
    public static function getAmountTotal(QueryBuilder $qb): int
    {
        $countQb = clone $qb;

        $count = $countQb->getDQLPart('groupBy')[0]?->getParts()[0] ?? 1;

        $countQb
            ->resetDQLPart('orderBy')
            ->resetDQLPart('groupBy')
            ->select("COUNT(DISTINCT $count)")
            ->setFirstResult(0)
            ->setMaxResults(null);

        return (int) $countQb->getQuery()->getSingleScalarResult();
    }

    /** @return array<string, string> */
    private static function getSelectParts(QueryBuilder $qb): array
    {
        $selectParts = [];
        foreach ($qb->getDQLPart('select') as $select) {
            /** @var Select $select */
            foreach ($select->getParts() as $selectPart) {
                if (str_starts_with($selectPart, 'NEW')) {
                    preg_match_all('/[\(,]\n *(.*) as ([\w_]*)/', $selectPart, $matches);
                    $newSelectArr = array_combine($matches[2], $matches[1]);
                    $selectParts = array_merge($selectParts, $newSelectArr);
                } else {
                    preg_match('/^(.*) as (.*)$/', $selectPart, $matches);
                    $selectParts[$matches[2]] = $matches[1];
                }
            }
        }

        return $selectParts;
    }
}
