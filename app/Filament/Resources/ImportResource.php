<?php

declare(strict_types=1);

namespace App\Filament\Resources;

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

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';

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
                Tables\Columns\TextColumn::make('serviceType.name')
                    ->label(__('add_point.type.service_type'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.full_name')
                    ->label(__('import.columns.user'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('file_name')
                    ->label(__('import.columns.file'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('import.columns.started_at'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label(__('import.columns.completed_at'))
                    ->placeholder(__('import.status.processing'))
                    ->formatStateUsing(function (string $state) {
                        return $state ? Carbon::createFromTimestamp($state)->diffForHumans() : 'N/A';
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('successful_rows')
                    ->label(__('import.columns.processed'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('error_rows')
                    ->label(__('import.columns.failed')),

            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('completed_at')
                    ->label(__('import.filters.is_completed'))
                    ->queries(
                        true:fn (Builder $query) => $query->whereNotNull('completed_at'),
                        false:fn (Builder $query) => $query->whereNull('completed_at'),
                        blank: fn (Builder $query) => $query,
                    ),
                Tables\Filters\TernaryFilter::make('with_errors')
                    ->label(__('import.filters.with_errors'))
                    ->queries(
                        true:fn (Builder $query) => $query->where('error_rows', '>', 0),
                        false:fn (Builder $query) => $query->where('error_rows', '=', 0),
                        blank: fn (Builder $query) => $query,
                    ),

                Tables\Filters\SelectFilter::make('service_type_id')
                    ->relationship('serviceType', 'name')
                    ->preload()
                    ->label(__('add_point.type.service_type'))
                    ->multiple(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'full_name')
                    ->label(__('import.columns.user'))
                    ->preload()
                    ->multiple(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)

            ->actions([
                Tables\Actions\ViewAction::make()->hidden(fn ($record) => $record->completed_at === null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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
            'index' => Pages\ListImports::route('/'),
            'view' => Pages\ViewImport::route('/{record}'),
        ];
    }
}
