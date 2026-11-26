<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contributions;

use App\Filament\Resources\Contributions\Pages\ListContributions;
use App\Models\Contribution;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ContributionResource extends Resource
{
    protected static ?string $model = Contribution::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-hand-raised';

    protected static string | \BackedEnum | null $activeNavigationIcon = 'heroicon-s-hand-raised';

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
                TextColumn::make('user.full_name')
                    ->label(__('contributions.column.user_name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label(__('contributions.column.user_email'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('point_id')
                    ->label(__('contributions.column.point_id'))
                    ->prefix('#'),

                TextColumn::make('model_type')
                    ->badge()
                    ->label(__('contributions.column.contribution_type'))
                    ->formatStateUsing(fn (string $state): string => __('enums.model_type.' . $state))
                    ->sortable(),

                TextColumn::make('point_type_name')
                    ->label(__('contributions.column.point_type')),

                TextColumn::make('problem_type_name')
                    ->label(__('contributions.column.problem_type'))
                    ->placeholder('-'),

                TextColumn::make('contribution_address')
                    ->label(__('contributions.column.address')),

                TextColumn::make('created_at')
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
            ->recordActions([
                Action::make('view')
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
            ->toolbarActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContributions::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
}
