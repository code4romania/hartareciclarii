<?php

declare(strict_types=1);

namespace App\Filament\Resources\MapPointsResource\Pages;

use App\Filament\Resources\PointResource;
use App\Models\ServiceType;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListMapPoints extends ListRecords
{
    protected static string $resource = PointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('map_points.buttons.create'))
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        $services = ServiceType::all();
        $tabs = [];
        foreach ($services as $service) {
            $tabs[$service->slug] = Tab::make($service->slug)->modifyQueryUsing(function ($query) use ($service) {
                return $query->where('service_type_id', $service->id);
            })->label($service->name);
        }
        return $tabs;
    }
}
