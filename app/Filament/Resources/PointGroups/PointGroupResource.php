<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointGroups;

use App\Filament\Resources\PointGroups\Pages\ListPointGroups;
use App\Models\PointGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PointGroupResource extends Resource
{
    protected static ?string $model = PointGroup::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('nav.settings');
    }

    public static function getModelLabel(): string
    {
        return __('common.points_group.singular');
    }

    public static function getPluralLabel(): ?string
    {
        return __('common.points_group.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label(__('common.name'))
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('common.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('points_count')
                    ->counts('points')
                    ->label(__('common.points_count'))
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) PointGroup::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPointGroups::route('/'),
        ];
    }
}
