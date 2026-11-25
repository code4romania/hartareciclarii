<?php

declare(strict_types=1);

namespace App\Filament\Resources\Imports;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Imports\Pages\ListImports;
use App\Filament\Resources\Imports\Pages\ViewImport;
use App\Filament\Resources\ImportResource\Pages;
use App\Models\Import;
use Carbon\Carbon;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ImportResource extends Resource
{
    protected static ?string $model = Import::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';

    protected static ?int $navigationSort = 4;

    public static function getLabel(): ?string
    {
        return __('import.singular');
    }

    public static function getPluralLabel(): ?string
    {
        return __('import.plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('nav.harta');
    }

    public static function getNavigationBadge(): ?string
    {
        return  (string) Cache::remember('import_count', now()->addMinutes(5), function () {
            return Import::count();
        });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serviceType.name')
                    ->label(__('add_point.type.service_type'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.full_name')
                    ->label(__('import.columns.user'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('file_name')
                    ->label(__('import.columns.file'))
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label(__('import.columns.started_at'))
                    ->searchable(),

                TextColumn::make('completed_at')
                    ->label(__('import.columns.completed_at'))
                    ->placeholder(__('import.status.processing'))
                    ->formatStateUsing(function (string $state) {
                        return $state ? Carbon::createFromTimestamp($state)->diffForHumans() : 'N/A';
                    })
                    ->searchable(),

                TextColumn::make('successful_rows')
                    ->label(__('import.columns.processed'))
                    ->searchable(),
                TextColumn::make('points_count')
                    ->counts('points')
                    ->label(__('common.points_count')),

                TextColumn::make('error_rows')
                    ->label(__('import.columns.failed')),

            ])
            ->filters([
                TernaryFilter::make('completed_at')
                    ->label(__('import.filters.is_completed'))
                    ->queries(
                        true:fn (Builder $query) => $query->whereNotNull('completed_at'),
                        false:fn (Builder $query) => $query->whereNull('completed_at'),
                        blank: fn (Builder $query) => $query,
                    ),
                TernaryFilter::make('with_errors')
                    ->label(__('import.filters.with_errors'))
                    ->queries(
                        true:fn (Builder $query) => $query->where('error_rows', '>', 0),
                        false:fn (Builder $query) => $query->where('error_rows', '=', 0),
                        blank: fn (Builder $query) => $query,
                    ),

                SelectFilter::make('service_type_id')
                    ->relationship('serviceType', 'name')
                    ->preload()
                    ->label(__('add_point.type.service_type'))
                    ->multiple(),

                SelectFilter::make('user_id')
                    ->relationship('user', 'full_name')
                    ->label(__('import.columns.user'))
                    ->preload()
                    ->multiple(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)

            ->recordActions([
                ViewAction::make()->hidden(fn ($record) => $record->completed_at === null),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->form([
                            Toggle::make('remove_points')
                                ->label(__('imports.remove_points'))
                                ->default(false),
                        ])->action(function (Collection $records, array $data) {
                            $records->each(function (Import $record) use ($data) {
                                if ($data['remove_points']) {
                                    $record->points()->delete();
                                }
                                $record->delete();
                            });
                        }),
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
            'index' => ListImports::route('/'),
            'view' => ViewImport::route('/{record}'),
        ];
    }
}
