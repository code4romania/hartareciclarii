<?php

namespace App\Filament\Resources\DuplicatesResource\Pages;

use App\Filament\Resources\DuplicatesResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListDuplicates extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = DuplicatesResource::class;

    // protected static string $view = 'filament.resources.duplicates.list';
    public function getTableQuery(): Builder
    {
        return static::getModel()::query()->with('firstPoint.getFields', 'secondPoint.getFields', 'secondPoint.getGroup', 'firstPoint.getGroup', 'getType');
    }

        protected function getHeaderActions(): array
        {
            return [
                // Actions\CreateAction::make(),
            ];
        }

        public function table(Table $table): Table
        {
            return $table
                ->columns([
                    Split::make([
                        TextColumn::make('getType.display_name')->label('Tip puncte')->sortable()->searchable(),
                        TextColumn::make('distance')->label('Distance puncte')->sortable()->formatStateUsing(fn (string $state): string => __("{$state} m"))->html(),
                    ]),
                    Panel::make([])->collapsible()->view('filament.resources.duplicates.list'),
                ])
                ->filters([
                    //
                ])
                ->actions([
                    Tables\Actions\ViewAction::make(),

                ])
                ->bulkActions([
                    // Tables\Actions\BulkActionGroup::make([
                    //     Tables\Actions\DeleteBulkAction::make(),
                    // ]),
                ]);
        }
}
