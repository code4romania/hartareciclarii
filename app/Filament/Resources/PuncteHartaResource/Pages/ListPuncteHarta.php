<?php

namespace App\Filament\Resources\PuncteHartaResource\Pages;

use App\Filament\Resources\PuncteHartaResource;
use App\Models\MapPointType as MapPointTypeModel;
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
        $types = MapPointTypeModel::get();
        $tabs = [];
        foreach ($types as $type)
        {
            $tabs[$type->type_name] = Tab::make($type->display_name)->modifyQueryUsing(function ($query) use ($type)
            {
                return $query->where('point_type_id', $type->id);
            });
        }

        return $tabs;
    }
}
