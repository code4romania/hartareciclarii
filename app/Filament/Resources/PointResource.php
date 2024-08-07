<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Point\Status;
use App\Filament\Resources\MapPointsResource\Pages;
use App\Models\Point;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
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

    protected static ?string $navigationGroup = 'Harta';

    // protected static ?string $navigationLabel = __('nav.map_points');

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function getContentPreviewDescription($get)
    {
        return [
            TextInput::make('type')
                ->readOnly()
                ->placeholder($get('type') ? MapPointTypeModel::find($get('type'))->display_name : $get('type')),
            Select::make('materials')
                ->label(__('map_points.materials'))
                ->options(RecycleMaterialModel::query()->pluck('name', 'id'))
                ->default($get('materials'))
                ->multiple(),
            TextInput::make('address')
                ->label(__('map_points.address'))
                ->readOnly()
                ->default($get('address')),
            TextInput::make('address_2')
                ->label(__('map_points.address'))
                ->readOnly()
                ->default($get('map')),
            TextInput::make('managed_by')
                ->label(__('map_points.managed_by'))
                ->readOnly()
                ->default($get('managed_by')),
            TextInput::make('website')
                ->label(__('map_points.website'))
                ->readOnly()
                ->default($get('website')),
            TextInput::make('email')
                ->label(__('map_points.email'))
                ->readOnly()
                ->default($get('email')),
            TextInput::make('phone_no')
                ->label(__('map_points.phone_no'))
                ->readOnly()
                ->default($get('phone_no')),
            Repeater::make('opening_hours')
                ->schema([
                    Select::make('start_day')
                        ->label(__('map_points.start_day'))
                        ->options(__('common.week_days')),
                    Select::make('end_day')
                        ->label(__('map_points.end_day'))
                        ->options(__('common.week_days')),
                    // >pluck('id')->toArray())
                    TimePicker::make('start_hour')
                        ->label(__('map_points.opening_time'))
                        ->seconds('false')
                        ->hoursStep(1)
                        ->minutesStep(10)
                        ->readOnly(),
                    TimePicker::make('end_hour')
                        ->label(__('map_points.closing_time'))
                        ->seconds('false')
                        ->hoursStep(1)
                        ->minutesStep(10)
                        ->readOnly(),
                ])
                ->default($get('opening_hours'))
                ->columns(4),
            Textarea::make('notes')
                ->label(__('map_points.notes'))
                ->readOnly()
                ->default($get('notes')),
            Checkbox::make('offers_transport')
                ->label(__('map_points.offers_transport'))
                ->default($get('notes')),
            Checkbox::make('offers_money')
                ->label(__('map_points.offers_money'))
                ->default($get('notes')),

        ];
    }

    public static function table(Table $table): Table
    {
        $actions = [
            Tables\Actions\ViewAction::make()
                ->label(__('map_points.buttons.details'))
                ->icon('heroicon-m-eye'),
            // Tables\Actions\Action::make('view-on-map')
            //     ->label(__('map_points.buttons.view_on_map'))
            //     ->icon('heroicon-m-map')
            //     ->url(fn (MapPointModel $record): string => route('map_points.map-view', $record))
            //     ->openUrlInNewTab(),

        ];
        if (auth()->user()->can('manage_map_points')) {
            $actions = array_merge($actions, [
                Tables\Actions\Action::make('validate-point')
                    ->label(__('map_points.buttons.validate'))
                    ->icon('heroicon-m-check')
                    ->url(fn (MapPointModel $record): string => route('map-points.validate', $record))
                    ->requiresConfirmation()
                    ->visible(function ($record): bool {
                        return $record->status == 0;
                    }),
                // Tables\Actions\EditAction::make()->label(__('map_points.buttons.edit')),
                Tables\Actions\DeleteAction::make()->label(__('map_points.buttons.delete')),
            ]);
        }

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
                                $state .= sprintf('<span class="badge badge-primary">+%s</span>', $icons->count() - 3);
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
                    ->color(fn (Status $state) => $state->color())
                    ->formatStateUsing(function (Status $state, $record) {
                        if ($record->issues->count() > 0) {
                            return Status::WITH_PROBLEMS->label();
                        }

                        return $state->label();
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make($actions),
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

                    // BulkAction::make('associate')->button()->action(fn (Collection $records) => ...),
                    BulkAction::make('change-status')
                        ->label(__('map_points.buttons.change_status'))
                        ->action(function (array $data, Collection $records): void {
                            foreach ($records as $record) {
                                $record->status = ! $record->status;
                                $record->save();
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-o-check')
                        ->color('warning')
                        ->requiresConfirmation(),
                    //                    BulkAction::make('updateGroup')
                    //                        ->label(__('map_points.buttons.set_group'))
                    //                        ->form([
                    //                            Select::make('group_id')
                    //                                ->label('Group')
                    //                                ->options(MapPointGroupModel::query()->pluck('name', 'id'))
                    //                                ->required()
                    //                                ->relationship('group', 'name')
                    //                                ->preload(),
                    //                        ])
                    //                        ->action(function (array $data, Collection $records): void {
                    //                            foreach ($records as $record) {
                    //                                $record->changeGroup($data['group_id']);
                    //                            }
                    //                        })
                    //                        ->icon('heroicon-o-user-group')
                    //                        ->color('info')
                    //                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation()->label(__('map_points.buttons.delete')),

                ]),
            ])->deferLoading();
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
            'create' => Pages\CreateMapPoints::route('/create'),
            'edit' => Pages\EditMapPoints::route('/{record}/edit'),
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
}
