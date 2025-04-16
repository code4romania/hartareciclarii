<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use App\DataTransferObjects\Location;
use App\Models\City;
use App\Services\Nominatim;
use Closure;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;

class LeafletAutocomplete extends Component
{
    use Concerns\HasName;

    protected string $view = 'filament-forms::components.fieldset';

    protected static string $sessionKey = 'leaflet-autocomplete';

    protected bool|Closure $isRequired = false;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();
        $static->columnSpanFull();

        return $static;
    }

    /**
     * @return array<Component>
     */
    public function getChildComponents(): array
    {
        $components = [];

        $components[] = Select::make('nominatim_autocomplete')
            ->label(__('map_points.fields.address'))
            ->native(false)
            ->dehydrated(false)
            ->allowHtml()
            ->live()
            ->searchDebounce(500) // 2 seconds
            ->searchingMessage(__(''))
            ->searchPrompt(__('Type to search...'))
            ->searchable()
            ->hint(new HtmlString(Blade::render('<x-filament::loading-indicator class="w-5 h-5" wire:loading wire:target="data.google_autocomplete" />')))
            ->columnSpanFull()
            ->getSearchResultsUsing(function (string $search, Set $set): array {
                $result = Nominatim::make()->search($search);

                $set('nominatim_autocomplete', null);
                $set('location.lat', null);
                $set('location.lng', null);

                Session::put(
                    static::$sessionKey,
                    $result->mapWithKeys(fn (Location $location) => [
                        $location->id => $location,
                    ]),
                );

                return $result
                    ->pluck('name', 'id')
                    ->toArray();
            })
            ->afterStateUpdated(function (?string $state, Set $set, Get $get, $livewire) {
                if (empty($state)) {
                    return;
                }

                if (filled($location = data_get(Session::pull(static::$sessionKey), $state))) {
                    $this->setLocation($location, $set, $livewire);
                }
            });

        $components[] = Placeholder::make('address_was_changed')
            ->label(__('map_points.new_address'))
            ->hidden(fn (Get $get) => empty($get('new_address')))
            ->content(fn (Get $get) => $get('new_address'));

        $components[] = Map::make('location')
            ->tilesUrl('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png')
            ->live()
            ->hiddenLabel()
            ->liveLocation()
            ->zoom(18)
            ->draggable(false)
            ->showMyLocationButton()
            ->hintAction(
                Action::make('change_pin_location')
                    ->label(__('map_points.change_pin_location'))
                    ->icon('heroicon-m-map-pin')
                    ->requiresConfirmation()
                    ->hidden(fn (Get $get) => empty($get('location.lat')))
                    ->form(fn (Get $get) => [
                        Map::make('new_point')
                            ->defaultLocation($get('location.lat'), $get('location.lng'))
                            ->tilesUrl('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png')
                            ->label(__('map_points.new_point'))
                            ->liveLocation()
                            ->live()
                            ->afterStateUpdated(function (?array $state, Set $set) {
                                $set('new_lat', $state['lat']);
                                $set('new_lng', $state['lng']);
                            })
                            ->zoom(18),

                        Placeholder::make('new_address')
                            ->label(__('map_points.new_address'))
                            ->inlineLabel()
                            ->hidden(fn (Get $get) => empty($get('new_lat')))
                            ->content(function (Get $get) {
                                $newLocation = Session::put(static::$sessionKey, Nominatim::make()->reverse($get('new_lat'), $get('new_lng')));

                                return $newLocation?->name;
                            }),

                        Toggle::make('change_address')
                            ->hidden(fn (Get $get) => empty($get('new_lat')))
                            ->label(__('map_points.change_address')),
                    ])
                    ->action(function (array $data, $livewire, Set $set) {
                        if (blank($data['change_address'])) {
                            $set('location', $data['new_point']);
                            $livewire->dispatch('refreshMap');

                            return;
                        }

                        if (filled($location = Session::pull(static::$sessionKey))) {
                            $this->setLocation($location, $set, $livewire);
                            $set('new_address', $location->name);
                            $set('nominatim_autocomplete', null);
                        }
                    })
            )
            ->columnSpanFull();

        $components[] = Hidden::make('county_id');
        $components[] = Hidden::make('city_id');
        $components[] = Hidden::make('address');

        return [
            Forms\Components\Grid::make(1)
                ->schema($components)
                ->hiddenLabel(),
        ];
    }

    protected function setLocation(Location $location, Set $set, \Livewire\Component $livewire): void
    {
        $city = City::search((string) $location->city)
            ->where('county', (string) $location->county)
            ->first();

        $set('location.lat', $location->center[0]);
        $set('location.lng', $location->center[1]);
        $set('county_id', $city->county_id);
        $set('city_id', $city->id);
        $set('address', $location->name);

        $livewire->dispatch('refreshMap');
    }
}
