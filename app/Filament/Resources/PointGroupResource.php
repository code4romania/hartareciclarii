<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PointGroupResource\Pages;
use App\Models\PointGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PointGroupResource extends Resource
{
    protected static ?string $model = PointGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return __('common.points_group.singular');
    }

    public static function getPluralLabel(): ?string
    {
        return __('common.points_group.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPointGroups::route('/'),
        ];
    }
}
