<?php
declare(strict_types=1);

namespace LessDatabase\Query\Builder\Applier\Values;

use RuntimeException;
use Doctrine\DBAL\ParameterType;
use LessValueObject\ValueObject;
use Doctrine\DBAL\Query\QueryBuilder;
use LessDatabase\Query\Builder\Applier\Applier;
use LessDatabase\Query\Builder\Helper\LabelHelper;
use LessValueObject\Enum\EnumValueObject;
use LessValueObject\Number\NumberValueObject;
use LessValueObject\String\StringValueObject;

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
     * @return iterable<string, string|int|bool|float|null>
     */
    protected function getProccessableKeys(QueryBuilder $builder): iterable
    {
        foreach ($this->values as $field => $value) {
            $value = $value instanceof ValueObject
                ? $this->toNativeValue($value)
                : $value;

            $key = LabelHelper::fromValue($value);
            $builder->setParameter(
                $key,
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

    private function toNativeValue(ValueObject $value): string|int|float
    {
        if ($value instanceof EnumValueObject) {
            return $value->value;
        }

        if ($value instanceof NumberValueObject) {
            return $value->value;
        }

        if ($value instanceof StringValueObject) {
            return $value->value;
        }

        $type = get_debug_type($value);

        throw new RuntimeException("No support for '{$type}'");
    }
}
