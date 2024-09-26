<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\Point\Status;
use App\Models\ServiceType;
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

        if (! $this->filters->has('service')) {
            return;
        }

        $builder->where(function (Builder $query) {
            ServiceType::query()
                ->whereIn('id', Arr::wrap($this->filters->get('service')))
                ->get()
                ->each(
                    fn (ServiceType $serviceType) => $query
                        ->orWhere(function (Builder $query) use ($serviceType) {
                            $query
                                ->where('service_type_id', $serviceType->id)
                                ->when(
                                    $this->filters->has($serviceType->slug),
                                    fn (Builder $query) => $query->whereIn('point_type_id', Arr::wrap($this->filters->get($serviceType->slug)))
                                );

                            match ($serviceType->slug) {
                                'waste_collection' => $this->filterByWasteCollection($query, $serviceType),
                                'repairs' => $this->filterByRepairs($query, $serviceType),
                                'reuse' => $this->filterByReuse($query, $serviceType),
                                'reduce' => $this->filterByReduce($query, $serviceType),
                                'donations' => $this->filterByDonations($query, $serviceType),
                                'other' => $this->filterByOther($query, $serviceType),
                            };
                        })
                );
        });
    }

    public function filterByWasteCollection(Builder $builder, ServiceType $serviceType): void
    {
        if ($this->filters->has('materials')) {
            $builder->whereHas('materials', function (Builder $query) {
                $query->whereIn('id', Arr::wrap($this->filters->get('materials')));
            });
        }
    }

    public function filterByRepairs(Builder $builder, ServiceType $serviceType): void
    {
        //
    }

    public function filterByReuse(Builder $builder, ServiceType $serviceType): void
    {
        // noop
    }

    public function filterByReduce(Builder $builder, ServiceType $serviceType): void
    {
        // noop
    }

    public function filterByDonations(Builder $builder, ServiceType $serviceType): void
    {
        // noop
    }

    public function filterByOther(Builder $builder, ServiceType $serviceType): void
    {
        // noop
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
}
