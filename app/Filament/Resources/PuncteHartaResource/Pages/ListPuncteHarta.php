<?php

namespace App\Filament\Resources\PuncteHartaResource\Pages;

use App\Filament\Resources\PuncteHartaResource;
use App\Models\MapPointService as MapPointServiceModel;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListPuncteHarta extends ListRecords
{
    protected static string $resource = PuncteHartaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('map_points.buttons.create'))->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        $services = MapPointServiceModel::get();
        $tabs = [];
        foreach ($services as $service)
        {
            $tabs[$service->name] = Tab::make($service->display_name)->modifyQueryUsing(function ($query) use ($service)
            {
                return $query->where('service_id', $service->id);
            });
        }

        return $tabs;
    }
}
