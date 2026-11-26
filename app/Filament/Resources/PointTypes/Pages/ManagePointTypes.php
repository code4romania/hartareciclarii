<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointTypes\Pages;

use App\Filament\Resources\PointTypes\PointTypeResource;
use App\Models\ServiceType;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManagePointTypes extends ManageRecords
{
    protected static string $resource = PointTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
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
