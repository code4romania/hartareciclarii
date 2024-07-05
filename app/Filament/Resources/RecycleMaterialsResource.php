<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\RecycleMaterialsResource\Pages;
use App\Models\Material;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RecycleMaterialsResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Settings';

    public static function getModelLabel(): string
    {
        return  __('materials.singular');
    }

    public static function getModelName(): string
    {
        return  __('materials.plural');
    }

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('materials.label'))
                    ->description(__('materials.heading'))
                    ->schema([
                        TextInput::make('name')->required()->unique(ignoreRecord: true),
                        Select::make('categories')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload(),
                        TextInput::make('icon'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icon')
                    ->formatStateUsing(fn (string $state): string => __("<img src='{$state}'>"))->html(),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('categories.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('points_count')
                    ->counts('points')
                    ->label(__('common.points_count'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->action(function ($record) {
                        $record->aliases()->delete();
                        $record->delete();
                    }),
                ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecycleMaterials::route('/'),
            'create' => Pages\CreateRecycleMaterials::route('/create'),
            'edit' => Pages\EditRecycleMaterials::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
}
