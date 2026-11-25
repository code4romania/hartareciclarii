<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports\Pages;

use Filament\Schemas\Schema;
use App\Filament\Resources\Reports\ReportResource;
use App\Models\Report;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components(
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
