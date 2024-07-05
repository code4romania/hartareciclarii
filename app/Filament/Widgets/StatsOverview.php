<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\IssueStatus;
use App\Models\Duplicate;
use App\Models\Issue;
use App\Models\Point;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(__('issues.label'), Issue::where('status', IssueStatus::New)->count())
                ->icon('heroicon-o-rectangle-stack')
                ->color('info'),
            Stat::make(__('map_points.title'), Point::count())
                ->icon('heroicon-o-rectangle-group')
                ->color('warning'),
            Stat::make(__('users.label'), User::count())
                ->icon('heroicon-o-rectangle-group')
                ->color('primary'),

            Stat::make(__('duplicates.label'), Duplicate::count())
                ->icon('heroicon-o-rectangle-group')
                ->color('primary'),

        ];
    }
}
