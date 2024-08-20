<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\IssueStatus;
use App\Filament\Resources\IssuesResource\Pages;
use App\Models\Issue;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class IssuesResource extends Resource
{
    protected static ?string $model = Issue::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'Harta';

    protected static ?int $navigationSort = 2;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(
            [

                TextEntry::make('issueTypes.name')
                    ->label(__('issues.columns.issue_type')),
            ]
        );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('point_id')
                    ->label(__('issues.columns.map_point_id'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->default(__('issues.columns.no_user'))
                    ->searchable(
                        query: fn (Builder $query, $search) => $query->whereHas(
                            'user',
                            fn (Builder $query) => $query->whereAny(['firstname', 'lastname', 'email', 'phone'], 'LIKE', '%' . $search . '%')
                        )
                    )
                    ->label(__('issues.columns.reporter'))->sortable(),

                TextColumn::make('created_at')
                    ->label(__('issues.columns.created_at'))
                    ->sortable(),
                TextColumn::make('serviceType.name')
                    ->label(__('issues.columns.service_type'))
                    ->sortable()
                    ->html(),
                TextColumn::make('issueTypes.name')
                    ->label(__('issues.columns.issue_type'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('issues.columns.status'))
                    ->sortable()
                    ->formatStateUsing(fn (Issue $record) => $record->status->label()),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('issue_type')
                    ->label(__('issues.columns.issue_type'))
                    ->relationship('issueTypes', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->options(IssueStatus::options())
                    ->label(__('issues.columns.status')),

                Filter::make('created_period')
                    ->label(__('issues.columns.created_at'))
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['created_from'] && ! $data['created_until']) {
                            return null;
                        }

                        return __('issues.filters.period') . Carbon::parse($data['created_from'])->format('d.m.Y') . ' - ' . Carbon::parse($data['created_until'])->format('d.m.Y');
                    })
                    ->form([
                        DatePicker::make('created_from')->label(__('issues.columns.created_from'))->required(),
                        DatePicker::make('created_until')->label(__('issues.columns.created_until'))->required(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions(
                [
                    Tables\Actions\ViewAction::make()
                        ->label(__('issues.buttons.details')),
                ],
            )
            ->headerActions(
                [
                    ExportAction::make()->exports([
                        ExcelExport::make('table')->fromTable(),
                    ]),
                ]
            )
            ->bulkActions([])
            ->deferLoading();
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
            'index' => Pages\ListIssues::route('/'),
            'create' => Pages\CreateIssues::route('/create'),
            'edit' => Pages\EditIssues::route('/{record}/edit'),
            'view' => Pages\ViewIssue::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::whereStatus(0)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return (string) static::getModel()::whereStatus(0)->count() > 1 ? 'danger' : 'primary';
    }

    public static function getLabel(): ?string
    {
        return __('issues.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('issues.label');
    }
}
