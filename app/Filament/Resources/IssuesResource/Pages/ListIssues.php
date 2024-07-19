<?php

declare(strict_types=1);

namespace App\Filament\Resources\IssuesResource\Pages;

use App\Enums\Point\ServiceType;
use App\Filament\Resources\IssuesResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListIssues extends ListRecords
{
    protected static string $resource = IssuesResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    public function getTabs(): array
    {
        return [

            Tab::make(ServiceType::WASTE_COLLECTION->label())
                ->modifyQueryUsing(function ($query) {
                    return $query->whereHas('serviceType', function ($query) {
                        $query->where('slug', ServiceType::WASTE_COLLECTION);
                    });
                }),

            Tab::make(ServiceType::REPAIRS->label())
                ->modifyQueryUsing(function ($query) {
                    return $query->whereHas('serviceType', function ($query) {
                        $query->where('slug', ServiceType::REPAIRS);
                    });
                }),

            Tab::make(__('point_types.other'))
                ->modifyQueryUsing(function ($query) {
                    return $query->whereDoesntHave('serviceType', function ($query) {
                        $query->whereIn('slug', [ServiceType::WASTE_COLLECTION, ServiceType::REPAIRS]);
                    });
                }),

        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('issues.header.list');
    }
}
