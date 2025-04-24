<?php
declare(strict_types=1);

namespace LesDatabaseTest\Query\Builder\Helper;

use RuntimeException;
use LesDatabase\Query\Builder\Helper\LabelHelper;
use LesValueObject\Enum\EnumValueObject;
use LesValueObject\Number\NumberValueObject;
use LesValueObject\String\StringValueObject;
use PHPUnit\Framework\TestCase;
use LesValueObject\String\AbstractStringValueObject;
use LesValueObject\Number\Int\AbstractIntValueObject;

/**
 * @covers \LesDatabase\Query\Builder\Helper\LabelHelper
 */
final class LabelHelperTest extends TestCase
{
    /**
     * @dataProvider getFieldValueTests
     */
    public function testCreateFieldValueKey(string|int|bool|float|EnumValueObject|NumberValueObject|StringValueObject|null $value, string $expectedKey): void
    {
        self::assertSame($expectedKey, LabelHelper::fromValue($value));
    }

    /**
     * @return array<array<mixed>>
     */
    public static function getFieldValueTests(): iterable
    {
        $enum = new class implements EnumValueObject {
            public string $value = 'enum';

            public static function cases(): array
            {
                throw new RuntimeException();
            }

            public static function from(string $value): static
            {
                throw new RuntimeException();
            }

            public function getValue(): string
            {
                return 'enum';
            }

            public function jsonSerialize(): mixed
            {
                throw new RuntimeException();
            }
        };

        $number = new class (3) extends AbstractIntValueObject
        {
            public static function getMinimumValue(): int
            {
                return 1;
            }

            public static function getMaximumValue(): int
            {
                return 5;
            }
        };

        $string = new class ('string') extends AbstractStringValueObject
        {
            public static function getMinimumLength(): int
            {
                return 1;
            }

            public static function getMaximumLength(): int
            {
                return 7;
            }
        };

        return [
            [true, 'b_true'],
            [false, 'b_false'],
            [null, 'null'],
            [1, 'i_pos_1'],
            [-1, 'i_neg_1'],
            [1.1, 'sf_3_777d45bbbcdf50d49c42c70ad7acf5fe'],
            ['foobar', 'sf_6_3858f62230ac3c915f300c664312c63f'],
            [$enum, 'sf_4_da45ec4be6574774008df9be683a4778'],
            [$number, 'i_pos_3'],
            [$string, 'sf_6_b45cffe084dd3d20d928bee85e7b0f21'],
        ];
    }
}
