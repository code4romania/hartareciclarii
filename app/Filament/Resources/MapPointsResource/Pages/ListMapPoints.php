<?php

declare(strict_types=1);

namespace App\Filament\Resources\MapPointsResource\Pages;

use App\Enums\Point\ServiceType;
use App\Filament\Resources\MapPointsResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListMapPoints extends ListRecords
{
    protected static string $resource = MapPointsResource::class;

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
        $services = ServiceType::options();
        $tabs = [];
        foreach ($services as $key => $value) {
            $tabs[$key] = Tab::make($value)->modifyQueryUsing(function ($query) use ($key) {
                return $query->where('service_type', $key);
            });
        }

        return $tabs;
    }
}
