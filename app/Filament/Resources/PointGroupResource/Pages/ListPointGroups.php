<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointGroupResource\Pages;

use App\Filament\Resources\PointGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPointGroups extends ListRecords
{
    protected static string $resource = PointGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
