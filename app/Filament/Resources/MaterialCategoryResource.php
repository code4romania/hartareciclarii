<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialCategoryResource\Pages;
use App\Models\MaterialCategory;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterialCategoryResource extends Resource
{
    protected static ?string $model = MaterialCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('nav.settings');
    }

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
                TextInput::make('name')
                    ->required()
                    ->label(__('materials.categories.fields.name'))
                    ->maxLength(255),

                TextInput::make('category_rank')
                    ->required()
                    ->label(__('materials.categories.fields.category_rank'))
                    ->numeric()
                    ->minValue(0),

                SpatieMediaLibraryFileUpload::make('icon')
                    ->label(__('materials.categories.fields.icon'))
                    ->rule('dimensions:max_width=128,max_height=128')
                    ->image()
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('icon')
                    ->label(__('materials.categories.fields.icon'))
                    ->toggleable(),

                TextColumn::make('name')
                    ->label(__('materials.categories.fields.name'))
                    ->searchable(),

                TextColumn::make('category_rank')
                    ->label(__('materials.categories.fields.category_rank'))
                    ->numeric()
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
                    ->searchable()
                    ->wrap(),

            ])
            ->filters([
                //
            ])
            ->reorderable('position')
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

        ];
    }
}
