<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ReportsResource\Pages;
use App\Models\Report as ReportModel;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReportsResource extends Resource
{
    protected static ?string $model = ReportModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Rapoarte';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('report.column.title'))
                    ->html(),
                TextColumn::make('created_at')
                    ->label(__('report.column.created_at'))
                    ->formatStateUsing(fn ($record) => $record->created_at->toString())
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton(),
            ]);
    }

    protected static function segmentsList(string $group, string $segments): string
    {
        return collect(explode(', ', $segments))
            ->filter()
            ->map(fn (string $segment) => __(sprintf('report.segment.value.%s.%s', $group, $segment)))
            ->join(', ');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\GenerateReport::route('/generate'),
            'saved' => Pages\ListReport::route('/'),
            'view' => Pages\ViewReport::route('/{record}'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('nav.reports');
    }

    public static function getLabel(): ?string
    {
        return __('nav.reports');
    }

    public static function getPluralLabel(): ?string
    {
        return __('nav.reports');
    }
}
