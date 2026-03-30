<?php

declare(strict_types=1);

namespace App\Repository\Trait;

use App\Doctrine\Hydration\IndexedScalarHydrator;
use Doctrine\ORM\Query;

trait PropertyFetchTrait
{
    abstract public function createQueryBuilder(string $alias, ?string $indexBy = null): \Doctrine\ORM\QueryBuilder;

    /**
     * Fetches specific entity properties as scalar values, without hydrating full objects.
     *
     * Allows return of associative array when $indexBy is set
     * without the need of declaring the index field on properties. {@see IndexedScalarHydrator}
     *
     * If only one property is selected, each result value is a scalar rather than an array.
     *
     * @param string|string[]       $properties One or more property names to fetch (e.g. 'id' or ['id', 'name'])
     * @param array<string, mixed>  $criteria   Filter conditions; null produces IS NULL, arrays produce IN (...)
     * @param array<string, string> $orderBy    ORDER BY clauses, e.g. ['name' => 'ASC']
     * @param int|null              $limit      Maximum number of rows to return
     * @param int|null              $offset     Number of rows to skip before returning results
     * @param string|null           $indexBy    Property to use as the result array key; activates {@see IndexedScalarHydrator}
     *
     * @return array<($indexBy is null ? int : string), ($properties is array ? array<string, mixed> : mixed)>
     *
     * @example
     * // Fetch player names indexed by jersey number, limited to the first 10 results:
     * $players = $repository->findPropertiesBy(
     *     properties: ['firstName', 'lastName'],
     *     criteria:   ['team' => $team],
     *     orderBy:    ['name' => 'ASC'],
     *     limit:      10,
     *     indexBy:    'number',
     * );
     * // Returns: [9 => ['firstName' => 'Gerd', 'lastName' => 'Müller'], 11 => ['firstName' => 'Gareth', 'lastName' => 'Bale'], ...]
     *
     * // Scalar result with index
     * $lastNameByJerseyNumber = $repository->findPropertiesBy(
     *     properties: 'lastName',
     *     indexeBy:   'number',
     * );
     * //Returns: [9 => 'Müller', 11 => 'Gareth', ...]
     */
    public function findPropertiesBy(
        string|array $properties,
        array $criteria = [],
        array $orderBy = [],
        ?int $limit = null,
        ?int $offset = null,
        ?string $indexBy = null,
    ): array {
        if (empty($properties)) {
            throw new \InvalidArgumentException('Properties array cannot be empty');
        }

        $alias = 'entity';

        $qb = $this->createQueryBuilder($alias);

        // add select fields
        $qb->resetDQLPart('select');

        foreach ((array) $properties as $property) {
            $qb->addSelect(sprintf('%s.%s', $alias, $property));
        }

        // add criteria to where clause
        $parameterIndex = 0;
        foreach ($criteria as $field => $value) {
            $parameterName = 'param' . $parameterIndex++;

            if (null === $value) {
                $qb->andWhere(sprintf('%s.%s IS NULL', $alias, $field));
            } elseif (is_array($value)) {
                $qb->andWhere(sprintf('%s.%s IN (:%s)', $alias, $field, $parameterName))
                    ->setParameter($parameterName, $value);
            } else {
                $qb->andWhere(sprintf('%s.%s = :%s', $alias, $field, $parameterName))
                    ->setParameter($parameterName, $value);
            }
        }

        // add orderBy
        foreach ($orderBy as $field => $direction) {
            $qb->addOrderBy(sprintf('%s.%s', $alias, $field), $direction);
        }

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        if (null !== $offset) {
            $qb->setFirstResult($offset);
        }

        $hydrator = Query::HYDRATE_SCALAR;
        if ($indexBy) {
            $qb->indexBy($alias, "$alias.$indexBy");

            if (!in_array($indexBy, (array) $properties)) {
                $qb->addSelect("$alias.$indexBy");
                $hydrator = IndexedScalarHydrator::NAME;
            } else {
                $hydrator = Query::HYDRATE_OBJECT;
            }
        }

        return $qb->getQuery()->getResult(hydrationMode: $hydrator);
    }
}
