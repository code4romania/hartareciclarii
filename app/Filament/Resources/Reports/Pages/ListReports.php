<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports\Pages;

use Filament\Schemas\Components\Tabs\Tab;
use App\Enums\ReportType;
use App\Filament\Resources\Reports\ReportResource;
use App\Filament\Resources\Reports\Actions\GenerateReport;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            GenerateReport::make(),
        ];
    }

    public function getTabs(): array
    {
        return collect(ReportType::options())
            ->map(
                fn ($label, $value) => Tab::make($label)->query(fn (Builder $query) => $query->where('type', $value))
            )
            ->toArray();
    }
}
