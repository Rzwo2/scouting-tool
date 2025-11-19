<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

class OptionalCamelCaseToSnakeCaseConverter extends CamelCaseToSnakeCaseNameConverter
{
    public const string CAMEL_CASE_TO_SNAKE_CASE = 'camelCaseToSnakeCase';

    public function normalize(string $propertyName, ?string $class = null, ?string $format = null, array $context = []): string
    {
        if (!isset($context[self::CAMEL_CASE_TO_SNAKE_CASE])) {
            return $propertyName;
        }

        return parent::normalize($propertyName, $class, $format, $context);
    }

    public function denormalize(string $propertyName, ?string $class = null, ?string $format = null, array $context = []): string
    {
        if (!isset($context[self::CAMEL_CASE_TO_SNAKE_CASE])) {
            return $propertyName;
        }

        return parent::denormalize($propertyName, $class, $format, $context);
    }
}
