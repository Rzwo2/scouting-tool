<?php

declare(strict_types=1);

namespace App\Repository;


trait RepositoryTrait
{
    /**
     * Find entities by criteria but return only specified properties
     *
     * @param array<string> $properties Properties to select (e.g., ['id', 'name', 'email'])
     * @param array<string, mixed> $criteria Criteria for filtering
     * @param array<string, string>|null $orderBy Order by fields
     * @param int|null $limit Limit results
     * @param int|null $offset Offset results
     * @return array<int, array<string, mixed>> Array of property arrays
     *
     * @example
     * $users = $repository->findPropertiesBy(
     *     ['id', 'name', 'email'],
     *     ['active' => true],
     *     ['name' => 'ASC'],
     *     10
     * );
     * // Returns: [['id' => 1, 'name' => 'John', 'email' => 'john@example.com'], ...]
     */
    public function findPropertiesBy(
        array $properties,
        array $criteria = [],
        array $orderBy = [],
        ?int $limit = null,
        ?int $offset = null
    ): array
    {
        if(empty($properties)){
            throw new \InvalidArgumentException('Properties array cannot be empty');
        }

        $alias = 'entity';

        $qb = $this->createQueryBuilder($alias);

        /** add properties to select clause */
        foreach ($properties as $property){
            $qb->addSelect(sprintf('%s.%s', $alias, $property));
        }

        /** add criteria to where clause */
        $parameterIndex = 0;
        foreach ($criteria as $field => $value) {
            $parameterName = 'param' . $parameterIndex++;

            if ($value === null) {
                $qb->andWhere(sprintf('%s.%s IS NULL', $alias, $field));
            } elseif (is_array($value)) {
                $qb->andWhere(sprintf('%s.%s IN (:%s)', $alias, $field, $parameterName))
                   ->setParameter($parameterName, $value);
            } else {
                $qb->andWhere(sprintf('%s.%s = :%s', $alias, $field, $parameterName))
                   ->setParameter($parameterName, $value);
            }
        }

        /** add orderBy */
        if ($orderBy !== null) {
            foreach ($orderBy as $field => $direction) {
                $qb->addOrderBy(sprintf('%s.%s', $alias, $field), $direction);
            }
        }

        return $qb->getQuery()->getScalarResult();
    }
}

