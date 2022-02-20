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
    public function __construct(protected array $values)
    {}

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
