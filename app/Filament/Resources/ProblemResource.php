<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProblemResource\Pages;
use App\Models\Problem\Problem;
use Carbon\Carbon;
use Dotswan\MapPicker\Infolists\MapEntry;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ProblemResource extends Resource
{
    protected static ?string $model = Problem::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'Harta';

    protected static ?int $navigationSort = 2;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(
            [
                Section::make()
                    ->heading()
                    ->columnSpan(1)
                    ->extraAttributes(['class' => 'h-full'])
                    ->schema(
                        [
                            TextEntry::make('type.name')
                                ->label(__('issues.columns.issue_type')),
                        ]
                    ),

                Section::make()
                    ->heading()
                    ->columnSpan(1)
                    ->extraAttributes(['class' => 'h-full'])
                    ->schema(
                        [
                            TextEntry::make('subTypes.name')
                                ->label(__('issues.columns.selected_subtypes')),
                        ]
                    )->hidden(fn (Problem $record) => blank($record->subTypes)),

                Section::make(__('issues.columns.description'))
                    ->columnSpan(1)
                    ->heading()
                    ->schema(
                        [
                            TextEntry::make('description')
                                ->label(__('issues.columns.description')),
                        ]
                    )->hidden(fn (Problem $record) => blank($record->description)),
                Grid::make()->schema(
                    [
                        Section::make()
                            ->columnSpan(1)
                            ->heading()
                            ->hidden(fn (Problem $record) => blank($record->collectedMaterials))
                            ->schema([
                                TextEntry::make('collectedMaterials')
                                    ->hint(__('issues.columns.collected_materials_hint'))
                                    ->label(__('issues.columns.collected_materials'))
                                    ->color('success'),
                            ]),

                        Section::make(__('issues.columns.not_collected_materials'))
                            ->heading()
                            ->columnSpan(1)
                            ->hidden(fn (Problem $record) => blank($record->notCollectedMaterials))
                            ->schema([
                                TextEntry::make('notCollectedMaterials')
                                    ->hint(__('issues.columns.not_collected_materials_hint'))
                                    ->color('danger')
                                    ->label(__('issues.columns.not_collected_materials')),
                            ]),
                    ]
                ),

                Grid::make()
                    ->schema([
                        Section::make(__('issues.columns.not_collected_materials'))
                            ->heading()
                            ->columnSpan(1)
                            ->schema([
                                TextEntry::make('point.address')
                                    ->color('danger')
                                    ->label(__('issues.columns.current_address')),
                            ]),
                        Section::make(__('issues.columns.not_collected_materials'))
                            ->heading()
                            ->columnSpan(1)
                            ->schema([
                                TextEntry::make('address')
                                    ->color('success')
                                    ->label(__('issues.columns.new_address')),
                            ]),

                    ])->hidden(fn (Problem $problem) => blank($problem->address)),

                Grid::make()
                    ->schema(
                        [
                            MapEntry::make('point.location')
                                ->label(__('issues.columns.current_location'))
                                ->state(fn (Problem $problem) => ['lat' => $problem->point->location->latitude, 'lng' => $problem->point->location->longitude])
                                ->zoom(16)
                                ->hint(fn (Problem $problem) => $problem->point->location),

                            MapEntry::make('location')
                                ->label(__('issues.columns.new_location'))
                                ->state(fn (Problem $problem) => ['lat' => $problem->location->latitude, 'lng' => $problem->location->longitude])
                                ->zoom(16)
                                ->hint(fn (Problem $problem) => $problem->location),

                        ]
                    )->hidden(fn (Problem $problem) => blank($problem->location)),

            ]
        );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('point_id')
                    ->label(__('issues.columns.map_point_id'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('issues.columns.created_at'))
                    ->sortable(),
                TextColumn::make('type.name')
                    ->label(__('issues.columns.service_type'))
                    ->sortable()
                    ->html(),
                TextColumn::make('status')
                    ->label(__('issues.columns.status'))
                    ->sortable()
                    ->formatStateUsing(fn (Problem $record) => $record->status->getLabel()),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('issues.columns.issue_type'))
                    ->relationship('type', 'name'),

                Filter::make('created_period')
                    ->label(__('issues.columns.created_at'))
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['created_from'] && ! $data['created_until']) {
                            return null;
                        }

                        return
                            __(
                                'issues.filters.period',
                                [
                                    'created_from' => $data['created_from'] ? Carbon::parse($data['created_from'])->format('d.m.Y') : null,
                                    'created_until' => $data['created_until'] ? Carbon::parse($data['created_until'])->format('d.m.Y') : null,
                                ],
                            );
                    })
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('issues.columns.created_from'))
                            ->required(),
                        DatePicker::make('created_until')
                            ->label(__('issues.columns.created_until'))
                            ->required(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions(
                [
                    Tables\Actions\ViewAction::make()
                        ->label(__('issues.buttons.details')),
                ],
            )
            ->headerActions(
                [
                    ExportAction::make()->exports([
                        ExcelExport::make('table')->fromTable(),
                    ]),
                ]
            )
            ->bulkActions([])
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
            'index' => Pages\ListProblem::route('/'),
            'view' => Pages\ViewProblem::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::whereNull('closed_at')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return (string) static::getModel()::whereNull('closed_at')->count() > 1 ? 'danger' : 'primary';
    }

    public static function getLabel(): ?string
    {
        return __('issues.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('issues.label');
    }
}
