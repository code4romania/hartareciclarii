<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProblemResource\Pages;

use App\Filament\Resources\ProblemResource;
use App\Models\ServiceType;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListProblem extends ListRecords
{
    protected static string $resource = ProblemResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    public function getTabs(): array
    {
        $dedicatedServiceTypes = ServiceType::query()
            ->where('has_dedicated_issues_tab', true)
            ->get();

        return $dedicatedServiceTypes
            ->mapWithKeys(fn (ServiceType $serviceType) => [
                $serviceType->slug => Tab::make($serviceType->name)
                    ->modifyQueryUsing(
                        fn (Builder $query) => $query->whereHas('point', fn (Builder $query) => $query->where('service_type_id', $serviceType->id))
                    ),
            ])
            ->put(
                'other',
                Tab::make(__('point_types.other'))
                    ->modifyQueryUsing(fn (Builder $query) => $query->whereDoesntHave('point', fn (Builder $query) => $query->whereNotIn('service_type_id', $dedicatedServiceTypes->pluck('id'))))
            )
            ->all();
    }

    public function getTitle(): string | Htmlable
    {
        return __('issues.header.list');
    }
}
