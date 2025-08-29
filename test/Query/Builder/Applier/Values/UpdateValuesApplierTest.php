<?php
declare(strict_types=1);

namespace LesDatabaseTest\Query\Builder\Applier\Values;

use RuntimeException;
use Doctrine\DBAL\Query\QueryBuilder;
use LesValueObject\Number\NumberValueObject;
use LesDatabase\Query\Builder\Applier\Values\UpdateValuesApplier;
use LesValueObject\Number\Int\IntValueObject;
use LesValueObject\String\StringValueObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesDatabase\Query\Builder\Applier\Values\UpdateValuesApplier
 */
final class UpdateValuesApplierTest extends TestCase
{
    public function testApply(): void
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
        ];

        $applier = new UpdateValuesApplier($values);

        $builder = $this->createMock(QueryBuilder::class);

        $builder
            ->expects(self::exactly(2))
            ->method('set');

        $applier->apply($builder);
    }
}
