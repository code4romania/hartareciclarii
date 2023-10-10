<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImportResource\Pages;
use App\Models\ImportExport as ImportExportModel;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ImportResource extends Resource
{
    protected static ?string $model = ImportExportModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square';

    protected static ?string $navigationGroup = 'Harta';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('file')->sortable()->searchable(),
                TextColumn::make('createdBy.fullname')->sortable()->searchable(),
                TextColumn::make('created_at')->sortable()->searchable(),
                TextColumn::make('started_at')->sortable()->searchable(),
                TextColumn::make('finished_at')->sortable()->searchable(),
                TextColumn::make('processed')->sortable()->searchable()->getStateUsing(
                    static function ($record): string
                    {
                        try
                        {
                            return \count($record->result['processed']);
                        }
                        catch(\Exception  $e)
                        {
                            return 0;
                        }
                    }
                )->html(),
                TextColumn::make('failed')->sortable()->searchable()->getStateUsing(
                    static function ($record): string
                    {
                        try
                        {
                            return \count($record->result['failed']);
                        }
                        catch(\Exception  $e)
                        {
                            return 0;
                        }
                    }
                )->html(),
                TextColumn::make('status')->sortable()->searchable()->formatStateUsing(function ($state, $record)
                {
                    if ($state == 2 && \count($record->result['errors']))
                    {
                        $errors = '';
                        foreach ($record->result['errors'] as $err)
                        {
                            $errors .= __('import.' . $err);
                        }

                        return $errors;
                    }

                    return match ($state)
                    {
                        0 => 'Pending',
                        1 => 'Processing',
                        2 => '<a href="' . self::getUrl('view_report', ['record'=>$record->id]) . '">View report</a>',
                    };
                })->html(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->iconButton(),
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
            'index' => Pages\ListImports::route('/'),
            'create' => Pages\CreateImport::route('/create'),
            'view_report' => Pages\ViewImport::route('/{record}'),
            // 'edit' => Pages\EditImport::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return 'Import';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Import';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereStatus(0)->count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
