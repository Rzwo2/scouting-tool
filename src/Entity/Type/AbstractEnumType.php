<?php

declare(strict_types=1);

namespace App\Entity\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Abscract class to define explicite enum types for SQL enums.
 * Add a unique name for the type and a list of values that define the enum.
 */
abstract class AbstractEnumType extends Type
{
    protected $name;
    protected $values = [];

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $values = array_map(fn ($val) => "'" . $val . "'", $this->values);

        return 'ENUM(' . implode(', ', $values) . ')';
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        return $value;
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (!in_array($value, $this->values, true)) {
            throw new \InvalidArgumentException("Invalid '" . $this->name . "' value.");
        }

        return $value;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
