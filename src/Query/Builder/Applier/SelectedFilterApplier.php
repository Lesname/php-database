<?php
declare(strict_types=1);

namespace LessDatabase\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;
use LessDatabase\Query\Builder\Helper\LabelHelper;
use LessValueObject\Composite\AbstractSelectedFilter;
use LessValueObject\Enum\EnumValueObject;
use LessValueObject\Enum\FilterMode;
use LessValueObject\Number\NumberValueObject;
use LessValueObject\String\StringValueObject;

final class SelectedFilterApplier implements Applier
{
    public function __construct(private string $field, private ?AbstractSelectedFilter $filter)
    {}

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
            $keys = ':' . implode(', :', $this->prepareInKeys($builder, $filter->selected));
            $builder->andWhere("{$this->field} IN ({$keys})");

            return;
        }

        $builder->andWhere("{$this->field} IS NOT NULL");
    }

    private function applyNone(QueryBuilder $builder, AbstractSelectedFilter $filter): void
    {
        if ($filter->selected && $filter->selected->count() > 0) {
            $keys = ':' . implode(', :', $this->prepareInKeys($builder, $filter->selected));
            $builder->andWhere("{$this->field} NOT IN ({$keys})");

            return;
        }

        $builder->andWhere("{$this->field} IS NULL");
    }

    private function applyAll(QueryBuilder $builder, AbstractSelectedFilter $filter): void
    {
        if ($filter->selected) {
            foreach ($this->prepareInKeys($builder, $filter->selected) as $key) {
                $builder->andWhere("{$this->field} = :{$key}");
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
            $key = LabelHelper::fromValue($item);
            $builder->setParameter($key, $item);

            $keys[] = $key;
        }

        return $keys;
    }
}
