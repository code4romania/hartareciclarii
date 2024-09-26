<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;

class Filter
{
    protected static array $allowedFilters = [
        'service',
        'reduce',
        'repairs',
        'reuse',
        'waste_collection',
        'materials',
        'can',
        'status',
    ];

    public static function isAllowed(string $filter): bool
    {
        return \in_array($filter, self::$allowedFilters);
    }

    /** @return array|float|int|string|bool|null */
    public static function getValue(mixed $value): mixed
    {
        if (empty($value)) {
            return $value;
        }

        if (\is_array($value)) {
            return collect($value)
                ->map(fn ($valueValue) => self::getValue($valueValue))
                ->all();
        }

        if (Str::contains($value, ',')) {
            return explode(',', $value);
        }

        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return $value;
    }
}
