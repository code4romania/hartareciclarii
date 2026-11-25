<?php

declare(strict_types=1);

namespace App\Filament\Resources\Imports\RelationManagers;

use App\Filament\Resources\Points\PointResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
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
                $query->withCount('problems');
            })
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => PointResource::getUrl('view', ['record' => $record]), shouldOpenInNewTab: true),
            ])
            ->heading('')
            ->description(__('import.imported_points.description'))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
