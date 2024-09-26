<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\Point\Status;
use App\Models\ServiceType;
use App\Services\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FilteredPointsScope implements Scope
{
    protected Collection $filters;

    public function __construct(Collection $filters)
    {
        $this->filters = $filters;
    }

    public function apply(Builder $builder, Model $model): void
    {
        if ($this->filters->isEmpty()) {
            return;
        }

        $this->filterByStatus($builder);

        if ($this->filters->has('service')) {
            $builder->where(
                fn (Builder $query) => ServiceType::query()
                    ->whereIn('id', Arr::wrap($this->filters->get('service')))
                    ->get()
                    ->each(fn (ServiceType $serviceType) => $this->filterByServiceType($query, $serviceType))
            );
        }
    }

    protected function filterByStatus(Builder $builder): void
    {
        if (! $this->filters->has('status')) {
            return;
        }

        $allowedStatuses = collect([
            Status::VERIFIED,
            Status::UNVERIFIED,
        ]);

        $status = collect($this->filters->get('status'))
            ->map(fn (string $status) => Status::tryFrom($status))
            ->filter(fn (?Status $status) => $allowedStatuses->contains($status))
            ->unique();

        // Checking all statuses is the same as checking none
        if ($status->count() === $allowedStatuses->count()) {
            return;
        }

        $status = $status->first();

        match ($status) {
            Status::VERIFIED => $builder->whereVerified(),
            Status::UNVERIFIED => $builder->whereUnverified(),
        };
    }

    protected function filterByServiceType(Builder $query, ServiceType $serviceType): void
    {
        $query->orWhere(function (Builder $query) use ($serviceType) {
            // Service type
            $query->where('service_type_id', $serviceType->id);

            // Point types
            if ($this->filters->has($serviceType->slug)) {
                $pointTypes = Arr::wrap($this->filters->get($serviceType->slug));

                if (filled($pointTypes)) {
                    $query->whereIn('point_type_id', $pointTypes);
                }
            }

            // Filter by materials
            if ($this->filters->has('materials')) {
                $materials = Arr::wrap(data_get($this->filters->get('materials'), $serviceType->slug));

                if (filled($materials)) {
                    $query->whereHas('materials', fn (Builder $q) => $q->whereIn('id', $materials));
                }
            }

            // Filter by characteristics
            if ($this->filters->has('can')) {
                $can = collect(data_get($this->filters->get('can'), $serviceType->slug))
                    ->map(fn (string $characteristic) => "can_{$characteristic}")
                    ->filter(fn (string $characteristic) => Filter::isAllowedCharacteristic($characteristic));

                if (filled($can)) {
                    $query->where(function (Builder $query) use ($can) {
                        $can->each(fn (string $characteristic) => $query->where($characteristic, true));
                    });
                }
            }
        });
    }
}
