<?php

declare(strict_types=1);

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
                TextColumn::make('file')
                    ->label(__('import.columns.file'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('createdBy.fullname')
                    ->label(__('import.columns.user'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('import.columns.created_at'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('started_at')
                    ->label(__('import.columns.started_at'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('finished_at')
                    ->label(__('import.columns.finished_at'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('processed')
                    ->label(__('import.columns.processed'))
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(
                        static function ($record): string {
                            try {
                                return \count($record->result['processed']);
                            } catch(\Exception  $e) {
                                return 0;
                            }
                        }
                    )
                    ->html(),
                TextColumn::make('failed')
                    ->label(__('import.columns.failed'))
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(
                        static function ($record): string {
                            try {
                                return \count($record->result['failed']);
                            } catch(\Exception  $e) {
                                return 0;
                            }
                        }
                    )
                    ->html(),
                TextColumn::make('status')
                    ->label(__('import.columns.status'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        if ($state == 2 && \count($record->result['errors'])) {
                            $errors = '';
                            foreach ($record->result['errors'] as $err) {
                                $errors .= __('import.' . $err);
                            }

                            return $errors;
                        }

                        return match ($state) {
                            0 => __('import.status.pending'),
                            1 => __('import.status.processing'),
                            2 => '<a href="' . self::getUrl('view_report', ['record' => $record->id]) . '">' . __('import.status.view') . '</a>',
                        };
                    })
                    ->html(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->iconButton()->hidden(function ($record) {
                    return $record->status != 0;
                }),
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
        return (string) static::getModel()::whereStatus(0)->count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function canAccess(): bool
    {
        return false;
    }
}
