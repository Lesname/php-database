<?php
declare(strict_types=1);

namespace LesDatabase\Query\Builder\Applier\Values;

use RuntimeException;
use Doctrine\DBAL\ParameterType;
use LesValueObject\ValueObject;
use Doctrine\DBAL\Query\QueryBuilder;
use LesDatabase\Query\Builder\Applier\Applier;
use LesValueObject\Enum\EnumValueObject;
use LesValueObject\Number\NumberValueObject;
use LesValueObject\String\StringValueObject;

abstract class AbstractValuesApplier implements Applier
{
    /**
     * @param array<string, string|int|bool|float|EnumValueObject|NumberValueObject|StringValueObject|null> $values
     */
    final public function __construct(protected array $values)
    {}

    /**
     * @param array<string, string|int|bool|float|EnumValueObject|NumberValueObject|StringValueObject|null> $values
     */
    public static function forValues(array $values): static
    {
        return new static($values);
    }

    public static function forValue(string $field, string|int|bool|float|EnumValueObject|NumberValueObject|StringValueObject|null $value): static
    {
        return new static([$field => $value]);
    }

    public function withValue(string $field, string|int|bool|float|EnumValueObject|NumberValueObject|StringValueObject|null $value): self
    {
        $clone = clone $this;
        $clone->values = [
            ...$this->values,
            $field => $value,
        ];

        return $clone;
    }

    /**
     * @return array<string, string|int|bool|float|EnumValueObject|NumberValueObject|StringValueObject|null>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return iterable<string, string>
     */
    protected function getProccessableKeys(QueryBuilder $builder): iterable
    {
        foreach ($this->values as $field => $value) {
            $value = $value instanceof EnumValueObject || $value instanceof NumberValueObject
                ? $value->value
                : $value;

            $key = $builder->createNamedParameter(
                $value,
                match (true) {
                    is_int($value) => ParameterType::INTEGER,
                    is_bool($value) => ParameterType::BOOLEAN,
                    default => ParameterType::STRING,
                },
            );

            yield $field => $key;
        }
    }
}
