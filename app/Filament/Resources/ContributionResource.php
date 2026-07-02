<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ContributionResource\Pages;
use App\Models\Contribution;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ContributionResource extends Resource
{
    protected static ?string $model = Contribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?string $activeNavigationIcon = 'heroicon-s-hand-raised';

    protected static ?int $navigationSort = 4;

    public static function getLabel(): ?string
    {
        return __('contributions.singular');
    }

    public static function getPluralLabel(): ?string
    {
        return __('contributions.plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('nav.settings');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->withPointData()->with('user');
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label(__('contributions.column.user_name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label(__('contributions.column.user_email'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('point_id')
                    ->label(__('contributions.column.point_id'))
                    ->prefix('#'),

                Tables\Columns\TextColumn::make('model_type')
                    ->badge()
                    ->label(__('contributions.column.contribution_type'))
                    ->formatStateUsing(fn (string $state): string => __('enums.model_type.' . $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('point_type_name')
                    ->label(__('contributions.column.point_type')),

                Tables\Columns\TextColumn::make('problem_type_name')
                    ->label(__('contributions.column.problem_type'))
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('contribution_address')
                    ->label(__('contributions.column.address')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('contributions.column.created_at'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('model_type')
                    ->label(__('contributions.column.contribution_type'))
                    ->options([
                        'place' => __('enums.model_type.place'),
                        'problem' => __('enums.model_type.problem'),
                    ]),

                SelectFilter::make('user_id')
                    ->label(__('contributions.column.user_name'))
                    ->relationship('user', 'full_name')
                    ->preload()
                    ->multiple()
                    ->searchable(),

                Filter::make('created_period')
                    ->label(__('contributions.column.created_at'))
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['created_from'] && ! $data['created_until']) {
                            return null;
                        }

                        return __(
                            'contributions.filters.period',
                            [
                                'created_from' => $data['created_from'] ? Carbon::parse($data['created_from'])->format('d.m.Y') : null,
                                'created_until' => $data['created_until'] ? Carbon::parse($data['created_until'])->format('d.m.Y') : null,
                            ],
                        );
                    })
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('contributions.column.created_from')),
                        DatePicker::make('created_until')
                            ->label(__('contributions.column.created_until')),
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
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('issues.buttons.details'))
                    ->icon('heroicon-m-eye')
                    ->url(fn (Contribution $record) => $record->model_type === 'place'
                        ? PointResource::getUrl('view', ['record' => $record->model_id])
                        : ProblemResource::getUrl('view', ['record' => $record->model_id])),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                ExportAction::make()->exports([
                    ExcelExport::make('contributions')
                        ->fromTable()
                        ->modifyQueryUsing(fn (Builder $query): Builder => $query->reorder('created_at', 'desc')),
                ]),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContributions::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
}
