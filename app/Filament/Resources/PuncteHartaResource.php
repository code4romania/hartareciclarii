<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PuncteHartaResource\Pages;
use App\Models\MapPoint as MapPointModel;
use App\Models\MapPointGroup as MapPointGroupModel;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PuncteHartaResource extends Resource
{
    protected static ?string $model = MapPointModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Harta';

    // protected static ?string $navigationLabel = __('nav.map_points');

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $actions = [
            Tables\Actions\ViewAction::make()->label(__('map_points.buttons.details'))->icon('heroicon-m-eye'),
            Tables\Actions\LinkAction::make('view-on-map')->label(__('map_points.buttons.view_on_map'))->icon('heroicon-m-map')
                ->url(fn (MapPointModel $record): string => route('map_points.map-view', $record)),

        ];
        if (auth()->user()->can('manage_map_points'))
        {
            $actions = array_merge($actions, [
                Tables\Actions\LinkAction::make('validate-point')->label(__('map_points.buttons.validate'))->icon('heroicon-m-check')
                    ->url(fn (MapPointModel $record): string => route('map-points.validate', $record))
                    ->requiresConfirmation()
                    ->visible(function ($record): bool
                    {
                        return $record->status == 0;
                    }),
                Tables\Actions\EditAction::make()->label(__('map_points.buttons.edit')),
                Tables\Actions\DeleteAction::make()->label(__('map_points.buttons.delete')),
            ]);
        }

        return $table
            ->columns([
                TextColumn::make('id')->label(__('map_points.id'))->sortable()->searchable(),
                TextColumn::make('getType.display_name')->label(__('map_points.point_type'))->searchable(),
                TextColumn::make('getManagedBy.value')->label(__('map_points.managed_by'))->sortable()->searchable()->wrap(),
                TextColumn::make('getMaterials.getParent.icon')->label(__('map_points.materials'))->sortable()->searchable()
                    ->formatStateUsing(function (string $state, $record)
                    {
                        $icons = collect(explode(',', $state))->unique();
                        $state = '<div style="display:inline-flex">';
                        foreach ($icons as $icon)
                        {
                            $state .= __("<img style='width:30px;padding:5px' src='" . str_replace(' ', '', $icon) . "'>");
                        }
                        $state = rtrim($state) . '</div>';

                        return $state;
                    })->html(),
                TextColumn::make('getCounty.value')->label(__('map_points.county'))->sortable()->searchable(),
                TextColumn::make('getCity.value')->label(__('map_points.city'))->sortable()->searchable(),
                TextColumn::make('getAddress.value')->label(__('map_points.address'))->sortable()->searchable()->wrap(),
                TextColumn::make('getGroup.name')->label(__('map_points.group'))->sortable()->searchable()->wrap(),

                BadgeColumn::make('status')
                    ->color(static function ($state, $record): string
                    {
                        if ($record->getIssues->count() > 0)
                        {
                            return 'danger';
                        }
                        if ((int) $state === 1)
                        {
                            return 'success';
                        }

                        return 'warning';
                    })
                    ->formatStateUsing(function (string $state, $record)
                    {
                        if ($record->getIssues->count() > 0)
                        {
                            return __('map_points.issues_found');
                        }
                        if ((int) $state === 1)
                        {
                            return __('map_points.verified');
                        }

                        return __('map_points.requires_verification');
                    })->html(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make($actions),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                    // BulkAction::make('associate')->button()->action(fn (Collection $records) => ...),
                    BulkAction::make('change-status')
                        ->label(__('map_points.buttons.change_status'))
                        ->action(function (array $data, Collection $records): void
                        {
                            foreach ($records as $record)
                            {
                                $record->status = !$record->status;
                                $record->save();
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-o-check')
                        ->color('warning')
                        ->requiresConfirmation(),
                    BulkAction::make('updateGroup')
                        ->label(__('map_points.buttons.set_group'))
                        ->form([
                            Select::make('group_id')
                                ->label('Group')
                                ->options(MapPointGroupModel::query()->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records): void
                        {
                            foreach ($records as $record)
                            {
                                $record->getGroup()->associate($data['group_id']);
                                $record->save();
                            }
                        })
                        ->icon('heroicon-o-user-group')
                        ->color('info')
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation()->label(__('map_points.buttons.delete')),

                ]),
            ]);
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
            'index' => Pages\ListPuncteHarta::route('/'),
            'create' => Pages\CreatePuncteHarta::route('/create'),
            'edit' => Pages\EditPuncteHarta::route('/{record}/edit'),
            'view' => Pages\ViewPunctHarta::route('/{record}'),

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
        return parent::getEloquentQuery()->manageble();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
