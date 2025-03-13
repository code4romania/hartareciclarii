<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\PointResource;
use App\Filament\Resources\ProblemResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContributionRelationManager extends RelationManager
{
    protected static string $relationship = 'contributions';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with('model');
            })
            ->columns([
                Tables\Columns\TextColumn::make('model.id')
                    ->label('ID')
                    ->prefix('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('model_type')
                    ->badge()
                    ->label(__('users.contribution_type'))
                    ->formatStateUsing(fn ($state): string => __('enums.model_type.' . $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('model.address')
                    ->label(__('map_points.address'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('model.created_at')
                    ->label(__('contributions.column.created_at'))
                    ->sortable(),

            ])
            ->actions([
                Tables\Actions\ViewAction::make()->url(fn ($record) => $record->model_type === 'place' ? PointResource::getUrl('view', ['record' => $record->model_id]) : ProblemResource::getUrl('view', ['record' => $record->model_id])),
            ]);
    }
}
