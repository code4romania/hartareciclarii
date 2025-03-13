<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Point\Status;
use App\Filament\Resources\PointResource\Pages;
use App\Models\Point;
use App\Models\PointGroup;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PointResource extends Resource
{
    protected static ?string $model = Point::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getColumns())
            ->filters([
                SelectFilter::make('status')
                    ->label(__('map_points.fields.status'))
                    ->options(Status::options()),
                SelectFilter::make('point_group_id')
                    ->label(__('map_points.fields.group'))
                    ->relationship('pointGroup', 'name')
                    ->multiple(),

                SelectFilter::make('point_type_id')
                    ->label(__('map_points.fields.point_type'))
                    ->relationship('pointType', 'name')
                    ->multiple(),

                SelectFilter::make('city_id')
                    ->label(__('map_points.city'))
                    ->relationship('city', 'name')
                    ->multiple(),

                SelectFilter::make('county_id')
                    ->label(__('map_points.county'))
                    ->relationship('county', 'name')
                    ->multiple(),

                TrashedFilter::make(),

            ])
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('map_points.buttons.details'))
                    ->icon('heroicon-m-eye'),
            ])
            ->headerActions(
                [
                    ExportAction::make()->exports([
                        ExcelExport::make('table')->fromTable()
                            ->except([
                                'materials.getParent.icon',
                            ])
                            ->withColumns([
                                Column::make('materials')
                                    ->formatStateUsing(function ($state) {
                                        $records = collect($state);

                                        return implode(',', $records->pluck('name')->toArray());
                                    }),
                            ]),
                    ]),
                ]
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                    BulkAction::make('change-status')
                        ->label(__('map_points.buttons.change_status'))
                        ->form([
                            Select::make('status')
                                ->label(__('map_points.fields.status'))
                                ->options(Status::options())
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records): void {
                            $recordsWithoutIssues = $records->filter(function ($record) {
                                return $record->issues->isEmpty();
                            });

                            $recordsWithoutIssues->map->changeStatus(Status::from($data['status']));

                            if ($records->count() === $recordsWithoutIssues->count()) {
                                Notification::make('success_status_changed')
                                    ->body(__('map_points.notifications.status_changed.success'))
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make('warning_status_changed')
                                    ->body(__('map_points.notifications.status_changed.warning', [
                                        'count' => $records->count() - $recordsWithoutIssues->count(),
                                    ]))
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-o-check')
                        ->color('warning')
                        ->requiresConfirmation(),


                    BulkAction::make('allocate_to_group')
                        ->label(__('map_points.buttons.set_group'))
                        ->form([
                            Select::make('group_id')
                                ->label(__('map_points.fields.group'))
                                ->options(PointGroup::all()->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records): void {
                            $group = PointGroup::find($data['group_id']);
                            Point::whereIn('id', $records->pluck('id')->toArray())
                                ->update([
                                    'point_group_id' => $group->id,
                                ]);

                            Notification::make('success_point_allocated_to_group')
                                ->body(__('map_points.notifications.point_allocated_to_group', [
                                    'number' => $records->count(),
                                    'group' => $group->name,
                                ]))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-o-users')
                        ->color('info')
                        ->requiresConfirmation(),

                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->contribution()->delete();
                                $record->delete();
                            });
                        })
                        ->label(__('map_points.buttons.delete')),

                ]),
            ])
            ->deferLoading();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMapPoints::route('/'),
            'view' => Pages\ViewMapPoint::route('/{record}'),

        ];
    }

    public static function getLabel(): ?string
    {
        return __('map_points.title');
    }

    public static function getPluralLabel(): ?string
    {
        return __('map_points.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('materials')->withCount('problems');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationGroup(): string
    {
        return __('nav.harta');
    }

    public static function getColumns()
    {
        return [
            TextColumn::make('id')
                ->label(__('map_points.id'))
                ->sortable()
                ->searchable(),

            TextColumn::make('serviceType.name')
                ->label(__('map_points.point_type')),

            TextColumn::make('administered_by')
                ->label(__('map_points.managed_by'))
                ->searchable()
                ->sortable()
                ->wrap(),

            TextColumn::make('materials.name')
                ->label(__('map_points.materials'))
                ->searchable()
                ->limitList(2),

            TextColumn::make('county.name')
                ->label(__('map_points.county'))
                ->searchable()
                ->sortable(),

            TextColumn::make('city.name')
                ->label(__('map_points.city'))
                ->sortable()
                ->searchable(),

            TextColumn::make('address')
                ->label(__('map_points.address'))
                ->searchable()
                ->wrap(),

            TextColumn::make('pointGroup.name')
                ->label(__('map_points.group'))
                ->sortable()
                ->wrap(),

            TextColumn::make('proximity_count')
                ->badge()
                ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                ->label(__('map_points.proximity_count')),

            TextColumn::make('status')
                ->sortable()
                ->badge()
                ->color(fn (Status $state) => $state->getColor())
                ->formatStateUsing(function (Status $state, $record) {
                    if ($record->problems_count > 0) {
                        return Status::WITH_PROBLEMS->getLabel();
                    }

                    return $state->getLabel();
                }),
        ];
    }
}
