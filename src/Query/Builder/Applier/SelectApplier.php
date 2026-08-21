<?php

declare(strict_types=1);

namespace LesDatabase\Query\Builder\Applier;

use Override;
use Doctrine\DBAL\Query\QueryBuilder;

final class SelectApplier implements Applier
{
    /** @var array<string, string> */
    private array $select = [];

    /**
     * @param array<string, string> $select
     *
     * @psalm-pure
     */
    public function __construct(iterable $select)
    {
        foreach ($select as $as => $sql) {
            $this->select[$as] = $sql;
        }
    }

    /**
     * @param array<string, string|array<string, string|array<string, string|array<string, string|array<string, string|array<string, string|array<string, string|array<string, string>>>>>>>> $select
     *
     * @psalm-suppress MixedArgumentTypeCoercion cannot be safely hinted
     * @psalm-pure
     */
    public static function fromNested(array $select): self
    {
        return new self(self::flatten($select));
    }

    /**
     * @param array<string, string|array<string, string|array<string, string|array<string, string|array<string, string|array<string, string|array<string, string|array<string, string>>>>>>>> $values
     * @param string $prefix
     *
     * @return array<string, string>
     *
     * @psalm-pure
     */
    private static function flatten(array $values, string $prefix = ''): array
    {
        $mapped = [];

        foreach ($values as $key => $value) {
            $key = ltrim("{$prefix}.{$key}", '.');

            if (is_array($value)) {
                $mapped = array_replace(
                    $mapped,
                    self::flatten($value, $key),
                );
            } else {
                $mapped[$key] = $value;
            }
        }

        return $mapped;
    }

    #[Override]
    public function apply(QueryBuilder $builder): QueryBuilder
    {
        return $builder->addSelect(...$this->makeSelect());
    }

    /**
     * @return iterable<int, string>
     *
     * @psalm-mutation-free
     */
    private function makeSelect(): iterable
    {
        foreach ($this->select as $as => $sql) {
            yield sprintf(
                '%s as "%s"',
                $sql,
                $as,
            );
        }
    }
}
