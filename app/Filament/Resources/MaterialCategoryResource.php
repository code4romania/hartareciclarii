<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialCategoryResource\Pages;
use App\Models\MaterialCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterialCategoryResource extends Resource
{
    protected static ?string $model = MaterialCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return __('materials.categories.singular');
    }

    public static function getPluralLabel(): string
    {
        return __('materials.categories.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label(__('materials.categories.fields.name'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('category_rank')
                    ->required()
                    ->label(__('materials.categories.fields.category_rank'))
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('materials.categories.fields.name'))
                    ->searchable(),
                TextColumn::make('category_rank')
                    ->numeric()
                    ->label(__('materials.categories.fields.category_rank'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('materials.name')
                    ->label(__('materials.plural'))
                    ->wrap()
                    ->searchable(),

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
        return (string) MaterialCategory::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterialCategories::route('/'),
            'create' => Pages\CreateMaterialCategory::route('/create'),
            'edit' => Pages\EditMaterialCategory::route('/{record}/edit'),
        ];
    }
}
