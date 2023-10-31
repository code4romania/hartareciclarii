<?php

/**
 * @Author: Bogdan Bocioaca
 * @Date:   2023-10-21 13:00:01
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-23 14:46:23
 */

declare(strict_types=1);

namespace App\Filament\Resources\ReportsResource\Concerns;

use App\Concerns\TabbedLayout;
use App\Filament\Resources\ReportsResource\Pages\GenerateReport;
use App\Filament\Resources\ReportsResource\Pages\ListIssues;
use App\Filament\Resources\ReportsResource\Pages\ViewReport;
use Filament\Navigation\NavigationItem;

trait HasTabs
{
    use TabbedLayout;

    public function getTabs(): array
    {
        return [
            // NavigationItem::make()
            //     ->label(__('report.section.generator'))
            //     // ->icon('icon-none')
            //     ->url(static::getResource()::getUrl('index'))
            //     ->isActiveWhen(fn (): bool => static::class === GenerateReport::class)
            //     ->activeIcon('heroicon-s-home'),

            // NavigationItem::make()
            //     ->label(__('report.section.list'))
            //     // ->icon('icon-none')
            //     ->url(static::getResource()::getUrl('saved'))
            //     ->isActiveWhen(fn (): bool => \in_array(static::class, [ListReports::class, ViewReport::class])),
        ];
    }

    public function isTableSelectionEnabled(): bool
    {
        return false;
    }

    public function getHeading(): string
    {
        return __('report.header.list');
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
