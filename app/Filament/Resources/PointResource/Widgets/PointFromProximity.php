<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointResource\Widgets;

use App\Filament\Resources\PointResource;
use App\Models\Point;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class PointFromProximity extends BaseWidget
{
    public ?Point $record = null;

    protected int | string | array $columnSpan='full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Point::query()
                    ->withCount('problems')
                    ->where('administered_by', $this->record->administered_by)
                    ->where('id', '!=', $this->record->id)
                    ->where('point_type_id', $this->record->point_type_id)
                    ->where('city_id', $this->record->city_id)
                    ->where('county_id', $this->record->county_id)
                    ->where('service_type_id', $this->record->service_type_id)
                    ->withDistanceSphere('location', $this->record->location)
                    ->whereDistance('location', $this->record->location, '<', 100)
            )
            ->heading(__('map_points.proximity_points'))
            ->columns([TextColumn::make('distance')->label(__('map_points.distance')), ...PointResource::getColumns()]);
    }
}
