<?php

declare(strict_types=1);

namespace App\Filament\Resources\RecycleMaterials\Pages;

use App\Filament\Resources\RecycleMaterials\RecycleMaterialsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRecycleMaterials extends ListRecords
{
    protected static string $resource = RecycleMaterialsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
