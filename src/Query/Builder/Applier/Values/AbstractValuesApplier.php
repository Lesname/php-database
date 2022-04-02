<?php
declare(strict_types=1);

namespace LessDatabase\Query\Builder\Applier\Values;

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
            if ($value instanceof NumberValueObject || $value instanceof EnumValueObject) {
                $value = $value->getValue();
            } elseif ($value instanceof StringValueObject) {
                $value = (string)$value;
            }

            $key = LabelHelper::fromValue($value);
            $builder->setParameter($key, $value);

            yield $field => $key;
        }
    }
}
