<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PointTypeResource\Pages;
use App\Models\PointType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PointTypeResource extends Resource
{
    protected static ?string $model = PointType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('nav.settings');
    }

    public static function getModelLabel(): string
    {
        return __('point_type.singular');
    }

    public static function getPluralLabel(): ?string
    {
        return __('point_type.plural');
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) PointType::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('common.name')),
                Forms\Components\Select::make('service_type_id')
                    ->required()
                    ->relationship('serviceType', 'name')
                    ->label(__('add_point.type.service_type')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('point_type.fields.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('serviceType.name')
                    ->label(__('point_type.fields.service_type_id'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('points_count')
                    ->counts('points')
                    ->label(__('point_type.fields.points_count')),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePointTypes::route('/'),
        ];
    }

    /**
     * @return bool
     */
    public static function isDiscovered(): bool
    {
        return true;
    }
}
