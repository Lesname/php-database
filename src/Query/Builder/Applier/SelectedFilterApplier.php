<?php
declare(strict_types=1);

namespace LesDatabase\Query\Builder\Applier;

use Override;
use Doctrine\DBAL\Query\QueryBuilder;
use LesValueObject\Composite\AbstractSelectedFilter;
use LesValueObject\Enum\EnumValueObject;
use LesValueObject\Enum\FilterMode;
use LesValueObject\Number\NumberValueObject;
use LesValueObject\String\StringValueObject;

final class SelectedFilterApplier implements Applier
{
    public function __construct(private string $field, private ?AbstractSelectedFilter $filter)
    {}

    #[Override]
    public function apply(QueryBuilder $builder): QueryBuilder
    {
        if ($this->filter) {
            match ($this->filter->mode) {
                FilterMode::None => $this->applyNone($builder, $this->filter),
                FilterMode::Any => $this->applyAny($builder, $this->filter),
                FilterMode::All => $this->applyAll($builder, $this->filter),
            };
        }

        return $builder;
    }

    private function applyAny(QueryBuilder $builder, AbstractSelectedFilter $filter): void
    {
        if ($filter->selected && $filter->selected->count() > 0) {
            $keys = implode(', ', $this->prepareInKeys($builder, $filter->selected));
            $builder->andWhere("{$this->field} IN ({$keys})");

            return;
        }

        $builder->andWhere("{$this->field} IS NOT NULL");
    }

    private function applyNone(QueryBuilder $builder, AbstractSelectedFilter $filter): void
    {
        if ($filter->selected && $filter->selected->count() > 0) {
            $keys = implode(', ', $this->prepareInKeys($builder, $filter->selected));
            $builder->andWhere("{$this->field} NOT IN ({$keys})");

            return;
        }

        $builder->andWhere("{$this->field} IS NULL");
    }

    private function applyAll(QueryBuilder $builder, AbstractSelectedFilter $filter): void
    {
        if ($filter->selected) {
            foreach ($this->prepareInKeys($builder, $filter->selected) as $key) {
                $builder->andWhere("{$this->field} = {$key}");
            }
        }
    }

    /**
     * @param QueryBuilder $builder
     * @param iterable<EnumValueObject|NumberValueObject|StringValueObject|null> $options
     *
     * @return array<string>
     */
    private function prepareInKeys(QueryBuilder $builder, iterable $options): array
    {
        $keys = [];

        foreach ($options as $item) {
            $value = $item instanceof EnumValueObject || $item instanceof NumberValueObject
                ? $item->value
                : $item;

            $keys[] = $builder->createNamedParameter($value);
        }

        return $keys;
    }
}
