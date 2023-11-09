<?php

namespace App\Filament\Resources\IssuesResource\Pages;

use App\Filament\Resources\IssuesResource;
use App\Models\MapPointType as MapPointTypeModel;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListIssues extends ListRecords
{
    protected static string $resource = IssuesResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    public function getTabs(): array
    {
        $map_point_types = MapPointTypeModel::whereHas('map_points.issues')->with('map_points.issues')->get();
        $this->type = request()->get('type', $map_point_types->first()->type_name);
        $tabs = [];

        foreach ($map_point_types as $type)
        {
            $tabs[$type->display_name] = Tab::make($type->display_name)->modifyQueryUsing(function ($query) use ($type)
            {
                return $query->whereIn('point_id', $type->map_points->pluck('id')->toArray());
            });
        }

        return $tabs;
    }

    public function getTitle(): string | Htmlable
    {
        return __('issues.header.list');
    }
}
