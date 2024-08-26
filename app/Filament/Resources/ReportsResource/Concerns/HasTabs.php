<?php

declare(strict_types=1);

namespace App\Filament\Resources\ReportsResource\Concerns;

use App\Concerns\TabbedLayout;
use Filament\Navigation\NavigationItem;

trait HasTabs
{
    use TabbedLayout;

    public function getTabs(): array
    {
        $this->type = request()->get('type', 'map_points');

        return [
            NavigationItem::make()
                ->label(__('report.tabs.map_points'))
                // ->icon('icon-none')
                ->url(static::getResource()::getUrl('index') . '?type=map_points')
                ->isActiveWhen(fn (): bool => $this->type === 'map_points')
                ->activeIcon('heroicon-s-home'),

            NavigationItem::make()
                ->label(__('report.tabs.issues'))
                // ->icon('icon-none')
                ->url(static::getResource()::getUrl('index') . '?type=issues')
                ->isActiveWhen(fn (): bool => $this->type === 'issues'),
            NavigationItem::make()
                ->label(__('report.tabs.users'))
                // ->icon('icon-none')
                ->url(static::getResource()::getUrl('index') . '?type=users')
                ->isActiveWhen(fn (): bool => $this->type === 'users'),
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
