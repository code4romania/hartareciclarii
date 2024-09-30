<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointTypeResource\Pages;

use App\Filament\Resources\PointTypeResource;
use App\Models\ServiceType;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManagePointTypes extends ManageRecords
{
    protected static string $resource = PointTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
