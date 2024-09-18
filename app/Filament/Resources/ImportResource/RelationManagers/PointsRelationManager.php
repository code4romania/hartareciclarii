<?php

declare(strict_types=1);

namespace App\Filament\Resources\ImportResource\RelationManagers;

use App\Filament\Resources\PointResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PointsRelationManager extends RelationManager
{
    protected static string $relationship = 'points';

    /**
     * @return mixed
     */
    public function getTableHeading(): string
    {
        return __('import.imported_points.title');
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('import.imported_points.title', ['number' => $ownerRecord->withCount('points')->points_count]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('address')
            ->columns(
                PointResource::getColumns()
            )
            ->filters([
                //
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->withCount('issues');
            })
            ->actions([
                ViewAction::make()
                    ->url(fn ($record) => PointResource::getUrl('view', ['record' => $record]), shouldOpenInNewTab: true),
            ])
            ->heading('')
            ->description(__('import.imported_points.description'))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
