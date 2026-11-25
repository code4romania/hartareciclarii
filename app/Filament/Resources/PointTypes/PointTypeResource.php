<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointTypes;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PointTypes\Pages\ManagePointTypes;
use App\Filament\Resources\PointTypeResource\Pages;
use App\Models\PointType;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PointTypeResource extends Resource
{
    protected static ?string $model = PointType::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('common.name')),
                Select::make('service_type_id')
                    ->required()
                    ->relationship('serviceType', 'name')
                    ->label(__('add_point.type.service_type')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('point_type.fields.name'))
                    ->searchable(),
                TextColumn::make('serviceType.name')
                    ->label(__('point_type.fields.service_type_id'))
                    ->searchable(),

                TextColumn::make('points_count')
                    ->counts('points')
                    ->label(__('point_type.fields.points_count')),
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

    public static function getPages(): array
    {
        return [
            'index' => ManagePointTypes::route('/'),
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
