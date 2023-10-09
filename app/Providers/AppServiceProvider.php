<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // tap($this->app->isLocal(), function (bool $shouldBeEnabled)
        // {
        //     Model::preventLazyLoading($shouldBeEnabled);
        //     Model::preventAccessingMissingAttributes($shouldBeEnabled);
        // });
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
        FilamentAsset::register([
            Js::make('custom-script', resource_path('js/custom.js')),
            Js::make('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'),
            Css::make('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),
            Js::make('leaflet-geocoding', 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js'),
            Css::make('leaflet-geocoding-css', 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css'),

        ]);
    }
}
