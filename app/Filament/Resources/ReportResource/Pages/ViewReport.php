<?php

declare(strict_types=1);

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Models\Report;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(
            [
                KeyValueEntry::make('results')
                    ->hiddenLabel()
                    ->keyLabel(fn (Report $report) => $report->label)
                    ->valueLabel(__('report.column.results'))
                    ->columnSpanFull(),
            ]
        );
    }
}
