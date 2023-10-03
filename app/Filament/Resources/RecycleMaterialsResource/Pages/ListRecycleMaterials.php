<?php

namespace App\Filament\Resources\RecycleMaterialsResource\Pages;

use App\Filament\Resources\RecycleMaterialsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecycleMaterials extends ListRecords
{
    protected static string $resource = RecycleMaterialsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
