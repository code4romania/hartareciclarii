<?php

declare(strict_types=1);

/**
 * @Author: Bogdan Bocioaca
 * @Date:   2023-10-21 13:06:49
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-31 09:01:26
 */

namespace App\Filament\Resources\ReportsResource\Pages;

use App\Filament\Resources\ReportsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReport extends ListRecords
{
    protected static string $resource = ReportsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
