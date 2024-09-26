<?php

declare(strict_types=1);

namespace App\Http\Filters;

use App\Enums\Point\Status;
use Illuminate\Database\Eloquent\Builder;

class TypeFilter extends Filter
{
    protected array $allowedStatuses = [
        Status::VERIFIED->value,
        Status::UNVERIFIED->value,
    ];

    public function __invoke(Builder $query, mixed $value, string $property): Builder
    {
        $serviceType = $this->getServiceTypes()
            ->firstWhere('slug', $property);

        debug($property, $value, $serviceType);

        return $query;
    }
}
