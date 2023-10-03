<?php

namespace App\Filament\Resources\PuncteHartaResource\Pages;

use App\Filament\Resources\PuncteHartaResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ViewPunctHarta extends ViewRecord
{
    protected static string $resource = PuncteHartaResource::class;

    protected static string $view = 'filament.resources.puncte-harta-resource.pages.view-punct-harta';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

     public function getBreadcrumb(): string
     {
         return static::$breadcrumb ?? __('filament-panels::resources/pages/view-record.breadcrumb');
     }

     public function getContentTabLabel(): ?string
     {
         return __('filament-panels::resources/pages/view-record.content.tab.label');
     }

     public function getTitle(): string | Htmlable
     {
         return Str::headline(static::getResource()::getPluralModelLabel()) . ' - View record';
     }

    // public function getHeader(): View
    // {
    //     return view('filament-panels::resources.header.index');
    // }
}
