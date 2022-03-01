<?php
declare(strict_types=1);

namespace LessDatabase\Query\Builder\Helper;

use BackedEnum;
use LessValueObject\Enum\EnumValueObject;
use LessValueObject\Number\NumberValueObject;
use LessValueObject\String\StringValueObject;

final class LabelHelper
{
    private function __construct()
    {}

    /**
     * @psalm-pure
     *
     * @psalm-suppress ImpurePropertyFetch
     */
    public static function fromValue(string|int|bool|float|EnumValueObject|NumberValueObject|StringValueObject|null $value): string
    {
        if ($value instanceof NumberValueObject || $value instanceof EnumValueObject) {
            $value = $value->getValue();
        } elseif ($value instanceof StringValueObject) {
            $value = (string)$value;
        }

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
}
