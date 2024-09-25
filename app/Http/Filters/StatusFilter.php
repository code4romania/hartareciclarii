<?php

declare(strict_types=1);

namespace App\Http\Filters;

use App\Enums\Point\Status;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class StatusFilter implements Filter
{
    protected array $allowedStatuses = [
        Status::VERIFIED->value,
        Status::UNVERIFIED->value,
    ];

    public function __invoke(Builder $query, mixed $value, string $property): Builder
    {
        $status = collect($value)
            ->filter(fn (string $status) => \in_array($status, $this->allowedStatuses))
            ->all();

        // Checking all statuses is the same as checking none
        if (\count(array_intersect($status, $this->allowedStatuses)) === \count($this->allowedStatuses)) {
            return $query;
        }

        return $query
            ->when(\in_array('verified', $status), fn (Builder $query) => $query->whereVerified())
            ->when(\in_array('unverified', $status), fn (Builder $query) => $query->whereUnverified());
    }
}
