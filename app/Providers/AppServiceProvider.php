<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Password;
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

            Filament::registerNavigationItems([

                // NavigationItem::make('Lista rapoarte')
                //     ->url('/admin/reports')
                //     ->icon('heroicon-o-presentation-chart-line')
                //     // ->activeIcon('heroicon-s-presentation-chart-line')
                //     ->group('Rapoarte')
                //     ->sort(2)
                //     // ->isActiveWhen(function ()
                //     // {
                //     //     return request()->route()->uri == 'admin/reports';
                //     // })
                //     ->hidden(auth()->user() && !auth()->user()->can('view_reports')),
            ]);
        });
        Filament::registerNavigationGroups([
            'Harta' => NavigationGroup::make()->label(__('nav.harta')),
            'Rapoarte' => NavigationGroup::make()->label(__('nav.reports')),
            'Settings' => NavigationGroup::make()->label(__('nav.settings')),
        ]);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        DatabaseNotifications::trigger('notifications.database-notifications-trigger');
        FilamentAsset::register([
            Js::make('custom-script', resource_path('js/custom.js')),
            Js::make('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'),
            Css::make('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),
            Js::make('leaflet-geocoding', 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js'),
            Css::make('leaflet-geocoding-css', 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css'),
        ]);
    }
}
