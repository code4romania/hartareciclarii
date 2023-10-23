<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DuplicatesResource\Pages;
use App\Models\Duplicate as DuplicateModel;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DuplicatesResource extends Resource
{
    protected static ?string $model = DuplicateModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Harta';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        dd($table);

        return $table
            ->columns([
                //
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
            // \App\Filament\Resources\DuplicatesManagerResource\RelationManagers\DuplicatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDuplicates::route('/'),
            // 'create' => Pages\CreateDuplicates::route('/create'),
            // 'edit' => Pages\EditDuplicates::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('nav.duplicates');
    }

    public static function getTableInfo(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type.display_name')->label('Tip puncte')->sortable()->searchable(),
                TextColumn::make('distance')->label('Distance puncte')->sortable(),
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
            ])->make();
    }

    public static function getLabel(): ?string
    {
        return 'Duplicate';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Duplicate';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'danger' : 'primary';
    }
}
