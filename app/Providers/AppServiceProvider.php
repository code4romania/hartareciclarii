<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::serving(function ()
        {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label('Settings')
                    // ->icon('heroicon-s-cog')
                    ->collapsed(),
            ]);
        });
        Filament::registerNavigationGroups([
            'Harta' => NavigationGroup::make()->label(__('nav.harta')),
            'Settings' => NavigationGroup::make()->label(__('nav.settings')),
        ]);
        Filament::serving(function ()
        {
            // Filament::registerNavigationItems([
            //     NavigationItem::make('Users')
            //         ->url('/admin/users')
            //         ->icon('heroicon-o-users')
            //         ->activeIcon('heroicon-s-users')
            //         ->group('Settings')
            //         ->sort(1)
            //         ->visible(auth()->check() ? auth()->user()->can('view_users') : false),
            // ]);
            // Filament::registerNavigationItems([
            //     NavigationItem::make('Permissions')
            //         ->url('/admin/permissions')
            //         ->icon('heroicon-o-no-symbol')
            //         ->activeIcon('heroicon-s-no-symbol')
            //         ->group('Settings')
            //         ->sort(2)
            //         ->visible(auth()->check() ? auth()->user()->can('view_permissions') : false),
            // ]);
            // Filament::registerNavigationItems([
            //     NavigationItem::make('Roles')
            //         ->url('/admin/roles')
            //         ->icon('heroicon-o-adjustments-horizontal')
            //         ->activeIcon('heroicon-s-adjustments-horizontal')
            //         ->group('Settings')
            //         ->sort(3)
            //         ->visible(auth()->check() ? auth()->user()->can('view_roles') : false),
            // ]);
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
