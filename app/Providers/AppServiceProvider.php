<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Filter;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        tap($this->app->isLocal(), function (bool $shouldBeEnabled) {
            Model::preventLazyLoading($shouldBeEnabled);
            Model::preventAccessingMissingAttributes($shouldBeEnabled);
        });

        $this->enforceMorphMap();

        Number::useLocale(app()->getLocale());

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
        $this->registerStrMacros();
        $this->registerRequestMacros();

        DatabaseNotifications::trigger('notifications.database-notifications-trigger');

        FilamentAsset::register([
            Js::make('custom-script', resource_path('js/admin.js')),
            Js::make('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'),
            Css::make('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),
            Js::make('leaflet-geocoding', 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js'),
            Css::make('leaflet-geocoding-css', 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css'),
        ]);

        JsonResource::withoutWrapping();

        // Remove next and previous links from pagination links
        JsonResource::macro('paginationInformation', function (Request $request, $paginated, $default) {
            array_shift($default['meta']['links']);
            array_pop($default['meta']['links']);

            return $default;
        });
    }

    protected function enforceMorphMap(): void
    {
        Relation::enforceMorphMap([
            'city' => \App\Models\City::class,
            'county' => \App\Models\County::class,
            'contribution' => \App\Models\Contribution::class,
            'material' => \App\Models\Material::class,
            'material_category' => \App\Models\MaterialCategory::class,
            'media' => \App\Models\Media::class,
            'place_group' => \App\Models\PointGroup::class,
            'place_type' => \App\Models\PointType::class,
            'place' => \App\Models\Point::class,
            'problem' => \App\Models\Problem\Problem::class,
            'service_type' => \App\Models\ServiceType::class,
            'temporary_upload' => \App\Models\TemporaryUpload::class,
            'user' => \App\Models\User::class,
        ]);
    }

    protected function registerStrMacros(): void
    {
        Str::macro('initials', fn (?string $value) => collect(explode(' ', (string) $value))
            ->map(fn (string $word) => Str::upper(Str::substr($word, 0, 1)))
            ->join(''));
    }

    protected function registerRequestMacros(): void
    {
        Request::macro('filters', function () {
            $filterParts = $this->input('filter', []);

            if (\is_string($filterParts)) {
                return collect();
            }

            return collect($filterParts)
                ->filter(fn (mixed $value, string $name) => Filter::isAllowed($name))
                ->map(fn (mixed $value) => Filter::getValue($value));
        });
    }
}
