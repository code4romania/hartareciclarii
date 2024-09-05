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
            ->columns([
                TextColumn::make('id')
                    ->label(__('map_points.id'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('serviceType.name')
                    ->label(__('map_points.point_type')),

                TextColumn::make('administered_by')
                    ->label(__('map_points.managed_by'))
                    ->wrap(),

                TextColumn::make('materials')
                    ->label(__('map_points.materials'))
                    ->sortable()
                    // ->searchable()
                    ->formatStateUsing(function (string $state, $record) {
                        $icons = collect(explode(',', $state))->unique();
                        $state = '<div style="display:inline-flex; flex-wrap:wrap">';
                        foreach ($icons as $index => $icon) {
                            if ($index < 3) {
                                $state .= "<img style='width:30px;padding:5px' src='" . str_replace(' ', '', $icon) . "'>";
                            } else {
                                $state .= \sprintf('<span class="badge badge-primary">+%s</span>', $icons->count() - 3);
                                break;
                            }
                        }
                        $state = rtrim($state) . '</div>';

                        return $state;
                    })
                    ->html(),
                TextColumn::make('county.name')
                    ->label(__('map_points.county')),
                TextColumn::make('city.name')
                    ->label(__('map_points.city')),

                TextColumn::make('address')
                    ->label(__('map_points.address'))
                    ->searchable()
                    ->wrap(),
                TextColumn::make('pointGroup.name')
                    ->label(__('map_points.group'))
                    ->sortable()
                    ->wrap(),

                TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn (Status $state) => $state->getColor())
                    ->formatStateUsing(function (Status $state, $record) {
                        if ($record->issues->count() > 0) {
                            return Status::WITH_PROBLEMS->getLabel();
                        }

                        return $state->getLabel();
                    }),
            ])
            ->filters([
                //
            ])
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

                    DeleteBulkAction::make()->requiresConfirmation()->label(__('map_points.buttons.delete')),

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

    public static function getNavigationLabel(): string
    {
        return __('nav.map_points');
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
        return parent::getEloquentQuery()->with('issues', 'materials');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationGroup(): string
    {
        return __('nav.harta');
    }
}
