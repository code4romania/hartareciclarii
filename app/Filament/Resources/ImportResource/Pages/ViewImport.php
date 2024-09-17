<?php

declare(strict_types=1);

namespace App\Filament\Resources\ImportResource\Pages;

use App\Filament\Resources\ImportResource;
use App\Filament\Resources\ImportResource\RelationManagers\PointsRelationManager;
use Carbon\Carbon;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewImport extends ViewRecord
{
    protected static string $resource = ImportResource::class;

    public function getRelationManagers(): array
    {
        return [
            PointsRelationManager::class,
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getHeading(): string
    {
        return __('import.view_heading', ['id' => $this->record->id]);
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('import.view_subheading', [
            'user' => $this->record->user->full_name,
            'created_at' => $this->record->created_at,
            'completed_at' => $this->record->completed_at ?
                Carbon::createFromTimestamp($this->record->completed_at)->format('Y-m-d h:m:s') :
                'N/A',
        ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(
            [
                Section::make(__('import.details'))
                    ->columns(2)
                    ->schema(
                        [
                            TextEntry::make('serviceType.name')
                                ->label(__('add_point.type.service_type')),
                            TextEntry::make('file_name')
                                ->label(__('import.columns.file')),
                            TextEntry::make('created_at')
                                ->label(__('import.columns.started_at')),
                            TextEntry::make('completed_at')
                                ->label(__('import.columns.completed_at'))
                                ->formatStateUsing(function (string $state) {
                                    return $state ? Carbon::createFromTimestamp($state)->diffForHumans() : 'N/A';
                                }),

                        ]
                    )->columnSpan(1),

                Section::make(__('import.failed.title'))
                    ->description($this->record->error_rows > 0 ? __('import.failed.description') : __('import.failed.no_failed'))
                    ->columns(2)
                    ->schema(
                        [
                            Actions::make([
                                Action::make('downloadAction')
                                    ->icon('heroicon-s-arrow-down-tray')
                                    ->color('primary')
                                    ->url(route('filament.imports.failed-rows.download', ['import' => $this->record], absolute: false), shouldOpenInNewTab: true)
                                    ->label(__('import.failed.download')),
                            ])->hidden($this->record->error_rows === 0),

                        ]
                    )->columnSpan(1)

                    ->extraAttributes(['class' => 'h-full']),

            ]
        )->columns(2);
    }
}
