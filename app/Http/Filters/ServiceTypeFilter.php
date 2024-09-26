<?php

declare(strict_types=1);

namespace App\Http\Filters;

use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilderRequest;

class ServiceTypeFilter extends Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): Builder
    {
        $filters = app(QueryBuilderRequest::class)->filters();

        $serviceTypes = $this->getServiceTypes()
            ->reject(function (ServiceType $serviceType) use ($filters) {
                return $filters->has($serviceType->slug)
                    && filled($filters->get($serviceType->slug));
            });

        if ($serviceTypes->isEmpty()) {
            return $query;
        }

        return $query->whereIn('service_type_id', $serviceTypes->pluck('id'));

        // return $query->whereIn;
    }
}
