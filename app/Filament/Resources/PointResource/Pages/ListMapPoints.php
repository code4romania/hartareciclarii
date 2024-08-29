<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointResource\Pages;

use App\Filament\Resources\PointResource;
use App\Filament\Resources\PointResource\Actions\Page\AddPoint;
use App\Models\ServiceType;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMapPoints extends ListRecords
{
    protected static string $resource = PointResource::class;

    protected function getHeaderActions(): array
    {
        return [
           AddPoint::make()
        ];
    }

    public function getTabs(): array
    {
        return ServiceType::all()
            ->mapWithKeys(fn (ServiceType $serviceType) => [
                $serviceType->slug => Tab::make($serviceType->slug)
                    ->label($serviceType->name)
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('service_type_id', $serviceType->id)),
            ])
            ->all();
    }
}
