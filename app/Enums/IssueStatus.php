<?php

declare(strict_types=1);

namespace App\Enums;

enum IssueStatus: int
{
    case New = 0;
    case Solved = 1;
    case Pending = 2;
    case Denied = 3;

    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->value] = __('issues.status.' . $case->value);
        }

        return $array;
    }
}
