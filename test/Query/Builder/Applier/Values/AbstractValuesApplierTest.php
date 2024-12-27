<?php
declare(strict_types=1);

namespace LessDatabaseTest\Query\Builder\Applier\Values;

use RuntimeException;
use Doctrine\DBAL\Query\QueryBuilder;
use LessValueObject\Number\NumberValueObject;
use LessDatabase\Query\Builder\Applier\Values\AbstractValuesApplier;
use LessValueObject\Number\Int\IntValueObject;
use LessValueObject\String\StringValueObject;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * @covers \LessDatabase\Query\Builder\Applier\Values\AbstractValuesApplier
 */
final class AbstractValuesApplierTest extends TestCase
{
    public function testProcessKey(): void
    {
        $string = new class implements StringValueObject {
            public string $value = 'string';

            public static function getMinimumLength(): int
            {
                throw new RuntimeException();
            }

            public static function getMaximumLength(): int
            {
                throw new RuntimeException();
            }

            public function getValue(): string
            {
                throw new RuntimeException();
            }

            public function __toString(): string
            {
                throw new RuntimeException();
            }

            public function jsonSerialize(): mixed
            {
                throw new RuntimeException();
            }
        };

        $number = new class implements IntValueObject {
            public int $value = 3;

            public static function getMinimumValue(): int
            {
                throw new RuntimeException();
            }

            public static function getMaximumValue(): int
            {
                throw new RuntimeException();
            }

            public function getValue(): int
            {
                throw new RuntimeException();
            }

            public static function getMultipleOf(): int|float
            {
                throw new RuntimeException();
            }

            public function isGreaterThan(NumberValueObject|float|int $value): bool
            {
                throw new RuntimeException();
            }

            public function isLowerThan(NumberValueObject|float|int $value): bool
            {
                throw new RuntimeException();
            }

            public function isSame(NumberValueObject|float|int $value): bool
            {
                throw new RuntimeException();
            }

            public function diff(NumberValueObject|float|int $with): float|int
            {
                throw new RuntimeException();
            }

            public function subtract(NumberValueObject|float|int $value): static
            {
                throw new RuntimeException();
            }

            public function append(NumberValueObject|float|int $value): static
            {
                throw new RuntimeException();
            }

            public function __toString(): string
            {
                throw new RuntimeException();
            }

            public function jsonSerialize(): mixed
            {
                throw new RuntimeException();
            }
        };

        $values = [
            'foo' => $string,
            'bar' => $number,
            'biz' => false,
        ];

        $mock = new class ($values) extends AbstractValuesApplier {
            public function apply(QueryBuilder $builder): QueryBuilder
            {
                return $builder;
            }

            public function getProccessableKeys(QueryBuilder $builder): iterable
            {
                yield from parent::getProccessableKeys($builder);
            }
        };
        $applier = $mock::forValues($values);

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::exactly(3))
            ->method('setParameter');

        $processed = $applier->getProccessableKeys($builder);
        $processed = $processed instanceof Traversable
            ? iterator_to_array($processed)
            : $processed;

        self::assertSame(
            [
                'foo' => 'sf_6_b45cffe084dd3d20d928bee85e7b0f21',
                'bar' => 'i_pos_3',
                'biz' => 'b_false',
            ],
            $processed,
        );
    }

    public function testForValue(): void
    {
        $string = new class implements StringValueObject {
            public string $value = 'string';

            public static function getMinimumLength(): int
            {
                throw new RuntimeException();
            }

            public static function getMaximumLength(): int
            {
                throw new RuntimeException();
            }

            public function getValue(): string
            {
                throw new RuntimeException();
            }

            public function __toString(): string
            {
                throw new RuntimeException();
            }

            public function jsonSerialize(): mixed
            {
                throw new RuntimeException();
            }
        };

        $mock = new class ([]) extends AbstractValuesApplier {
            public function apply(QueryBuilder $builder): QueryBuilder
            {
                return $builder;
            }

            public function getProccessableKeys(QueryBuilder $builder): iterable
            {
                yield from parent::getProccessableKeys($builder);
            }
        };
        $applier = $mock::forValue('foo', $string);

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::once())
            ->method('setParameter')
            ->with('sf_6_b45cffe084dd3d20d928bee85e7b0f21', 'string');

        $processed = $applier->getProccessableKeys($builder);
        $processed = $processed instanceof Traversable
            ? iterator_to_array($processed)
            : $processed;

        self::assertSame(
            ['foo' => 'sf_6_b45cffe084dd3d20d928bee85e7b0f21'],
            $processed,
        );
    }

    public function testWithValue(): void
    {
        $mock = $this->getMockForAbstractClass(
            AbstractValuesApplier::class,
            [['foo' => 'bar']],
        );

        self::assertSame(['foo' => 'bar'], $mock->getValues());

        $clone = $mock->withValue('bar', 'foo');

        self::assertSame(['foo' => 'bar'], $mock->getValues());
        self::assertSame(
            [
                'foo' => 'bar',
                'bar' => 'foo',
            ],
            $clone->getValues(),
        );
    }
}
