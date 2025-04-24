<?php
declare(strict_types=1);

namespace LesDatabase\Query\Builder\Helper;

use RuntimeException;
use LesValueObject\ValueObject;
use LesValueObject\Enum\EnumValueObject;
use LesValueObject\Number\NumberValueObject;
use LesValueObject\String\StringValueObject;

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

        $string = (string)$value;
        $length = strlen($string);
        $hash = md5($string);

        return "sf_{$length}_{$hash}";
    }

    /**
     * @psalm-pure
     */
    private static function toNativeValue(ValueObject $value): string|int|float
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
