<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        tap($this->app->isLocal(), function (bool $shouldBeEnabled) {
            Model::preventLazyLoading($shouldBeEnabled);
            Model::preventAccessingMissingAttributes($shouldBeEnabled);
        });

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label('Settings')
                    // ->icon('heroicon-s-cog')
                    ->collapsed(),
            ]);

            Filament::registerNavigationItems([
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
            Js::make('custom-script', resource_path('js/admin.js')),
            Js::make('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'),
            Css::make('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),
            Js::make('leaflet-geocoding', 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js'),
            Css::make('leaflet-geocoding-css', 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css'),
        ]);

        JsonResource::withoutWrapping();
    }
}
