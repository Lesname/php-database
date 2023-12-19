<?php
declare(strict_types=1);

namespace LessDatabase\Query\Builder\Helper;

use BackedEnum;
use RuntimeException;
use LessValueObject\ValueObject;
use LessValueObject\Enum\EnumValueObject;
use LessValueObject\Number\NumberValueObject;
use LessValueObject\String\StringValueObject;

final class LabelHelper
{
    private function __construct()
    {}

    /**
     * @psalm-pure
     */
    public static function fromValue(string|int|bool|float|EnumValueObject|NumberValueObject|StringValueObject|null $value): string
    {
        $value = $value instanceof ValueObject
            ? self::toNativeValue($value)
            : $value;

        if (is_bool($value)) {
            return $value ? 'b_true' : 'b_false';
        }

        if (is_int($value)) {
            return $value >= 0
                ? "i_pos_{$value}"
                : 'i_neg_' . abs($value);
        }

        if ($value === null) {
            return 'null';
        }

        return 'sf_' . md5((string)$value);
    }

    /**
     * @psalm-pure
     */
    private static function toNativeValue(ValueObject $value): string|int|float
    {
        if ($value instanceof EnumValueObject) {
            return $value->getValue();
        }

        if ($value instanceof NumberValueObject) {
            return $value->getValue();
        }

        if ($value instanceof StringValueObject) {
            return $value->getValue();
        }

        $type = get_debug_type($value);

        throw new RuntimeException("No support for '{$type}'");
    }
}
