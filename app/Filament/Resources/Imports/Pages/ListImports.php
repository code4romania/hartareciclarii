<?php

declare(strict_types=1);

namespace App\Filament\Resources\Imports\Pages;

use Filament\Actions\ImportAction;
use App\Filament\Imports\PointImporter;
use App\Filament\Resources\Imports\ImportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImports extends ListRecords
{
    protected static string $resource = ImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(PointImporter::class)
                ->color('primary')
                ->label(__('map_points.buttons.import')),
        ];
    }
}
