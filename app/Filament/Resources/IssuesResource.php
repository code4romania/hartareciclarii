<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IssuesResource\Pages;
use App\Models\Issue;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $actions = [
            Tables\Actions\ViewAction::make()
                ->label(__('issues.buttons.details')),
            // ->icon('heroicon-m-eye'),
        ];

        return $table
            ->columns([
                TextColumn::make('point_id')
                    ->label(__('issues.columns.map_point_id'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('reporter.fullname')
                    ->label(__('issues.columns.reporter'))
                    ->searchable(query: function (Builder $query, string $search): Builder
                    {
                        return $query->whereHas('reporter', function ($q) use ($search)
                        {
                            $q->whereRaw(\DB::raw("CONCAT(firstname,' ',lastname) like '%$search%'"));
                        });
                    })
                    ->html(),
                TextColumn::make('created_at')
                    ->label(__('issues.columns.created_at'))
                    ->sortable(),
                TextColumn::make('type.title')
                    ->label(__('issues.columns.issue_type'))
                    ->sortable()
                    ->html(),
                TextColumn::make('status')
                    ->label(__('issues.columns.status'))
                    ->sortable()
                    ->formatStateUsing(function ($state)
                    {
                        return __('issues.status.' . $state);
                    }),

            ])
            ->filters([
                //
            ])
            ->actions(
                $actions,
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
        return static::getModel()::whereStatus(0)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::whereStatus(0)->count() > 1 ? 'danger' : 'primary';
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
