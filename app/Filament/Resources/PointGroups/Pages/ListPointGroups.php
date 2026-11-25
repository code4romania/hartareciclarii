<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointGroups\Pages;

use App\Filament\Resources\PointGroups\PointGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPointGroups extends ListRecords
{
    protected static string $resource = PointGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
