<?php

declare(strict_types=1);

namespace App\Filament\Resources\ReportsResource\Pages;

use App\Contracts\Pages\WithTabs;
use App\Filament\Resources\ReportsResource;
use App\Filament\Resources\ReportsResource\Concerns;
use Filament\Resources\Pages\ViewRecord;

class ViewReport extends ViewRecord implements WithTabs
{
    use Concerns\HasTabs;

    protected static string $resource = ReportsResource::class;

    protected static string $view = 'filament.resources.reports-resource.pages.view';

    public static ?string $title = null;

    protected function getForms(): array
    {
        return [
            'form',
        ];
    }
}
