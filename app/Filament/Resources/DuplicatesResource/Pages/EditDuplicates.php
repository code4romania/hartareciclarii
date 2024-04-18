<?php

declare(strict_types=1);

namespace App\Filament\Resources\DuplicatesResource\Pages;

use App\Filament\Resources\DuplicatesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDuplicates extends EditRecord
{
    protected static string $resource = DuplicatesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
