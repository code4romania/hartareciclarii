<?php

declare(strict_types=1);

namespace App\Http\Filters;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilderRequest;

final class Filters
{
    protected static array $allowedFilters = [
        'service',
        'status',
    ];

    public static function selected(Request $request): array
    {
        $request = QueryBuilderRequest::fromRequest($request);

        $allowedFilters = collect(static::allowed())->map->getName();

        return $request->filters()
            ->filter(fn ($value, string $key) => $allowedFilters->contains($key))
            ->all();
    }

    public static function allowed(): array
    {
        return [
            AllowedFilter::exact('service', 'service_type_id'),
            AllowedFilter::exact('type', 'point_type_id'),
            AllowedFilter::custom('status', new StatusFilter),
        ];
    }
}
