<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserGroups;

use App\Filament\Resources\UserGroups\Pages\ListUserGroups;
use App\Models\UserGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserGroupResource extends Resource
{
    protected static ?string $model = UserGroup::class;

    protected static ?int $navigationSort = 5;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('users.group.singular');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('nav.settings');
    }

    public static function getPluralLabel(): string
    {
        return __('users.group.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('users.group.name'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('users.group.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label(__('users.group.users_count'))
                    ->counts('users'),
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

    public static function getPages(): array
    {
        return [
            'index' => ListUserGroups::route('/'),
        ];
    }
}
