<?php

declare(strict_types=1);

namespace App\Filament\Resources\ImportResource\Pages;

use App\Filament\Imports\PointImporter;
use App\Filament\Resources\ImportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImports extends ListRecords
{
    protected static string $resource = ImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->importer(PointImporter::class)
                ->label(__('map_points.buttons.import')),
        ];
    }
}
