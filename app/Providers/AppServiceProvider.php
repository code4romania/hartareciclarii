<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\City;
use App\Models\Contribution;
use App\Models\County;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Media;
use App\Models\Point;
use App\Models\PointGroup;
use App\Models\PointType;
use App\Models\Problem\Problem;
use App\Models\ServiceType;
use App\Models\TemporaryUpload;
use App\Models\User;
use App\Services\Filter;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Filament\Schemas\Components\Section;
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

        $this->setSeoDefaults();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerStrMacros();
        $this->registerRequestMacros();

        DatabaseNotifications::trigger('notifications.database-notifications-trigger');

        FileUpload::configureUsing(function (FileUpload $fileUpload) {
            $fileUpload->visibility('public');
        });

        Section::configureUsing(fn (Section $section) => $section
            ->columnSpanFull());

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
            'city' => City::class,
            'county' => County::class,
            'contribution' => Contribution::class,
            'material' => Material::class,
            'material_category' => MaterialCategory::class,
            'media' => Media::class,
            'place_group' => PointGroup::class,
            'place_type' => PointType::class,
            'place' => Point::class,
            'problem' => Problem::class,
            'service_type' => ServiceType::class,
            'temporary_upload' => TemporaryUpload::class,
            'user' => User::class,
        ]);
    }

    protected function setSeoDefaults(): void
    {
        seo()
            ->withUrl()
            ->title(
                default: config('app.name'),
                modifier: fn (string $title) => $title . ' — ' . config('app.name')
            )
            // TODO: Add a default description
            // ->description(default: '')
            ->locale(app()->getLocale())
            ->favicon()
            ->twitter();
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
            $filterParts = $this->query();

            if (\is_string($filterParts)) {
                return collect();
            }

            return collect($filterParts)
                ->filter(fn (mixed $value, string $name) => Filter::isAllowed($name))
                ->map(fn (mixed $value) => Filter::getValue($value));
        });
    }
}
