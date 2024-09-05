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
use Illuminate\Support\HtmlString;

class LeafletAutocomplete extends Component
{
    use Concerns\HasName;

    protected string $view = 'filament-forms::components.fieldset';

    protected bool|Closure $isRequired = false;

    protected int|Closure $autocompleteSearchDebounce = 500; // 2 seconds

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
            ->searchDebounce($this->getAutocompleteSearchDebounce()) // 2 seconds
            ->searchingMessage(__(''))
            ->searchPrompt(__('Type to search...'))
            ->searchable()
            ->hint(new HtmlString(Blade::render('<x-filament::loading-indicator class="h5 w-5" wire:loading wire:target="data.google_autocomplete" />')))
            ->columnSpanFull()
            ->getSearchResultsUsing(function (string $search, Set $set): array {
                $result = Nominatim::make()->search($search);
                \Cache::forget('nominatim_search_' . auth()->user()->id);
                $set('location.lat', null);
                $set('location.lng', null);

                \Cache::remember('nominatim_search_' . auth()->user()->id, 60, function () use ($result) {
                    return $result->mapWithKeys(function ($item) {
                        return [$item->id => $item];
                    });
                });

                return $result->pluck('name', 'id')
                    ->toArray();
            })
            ->afterStateUpdated(function (?string $state, Set $set, $livewire) {
                if (empty($state)) {
                    return;
                }
                if (! \Cache::has('nominatim_search_' . auth()->user()->id)) {
                    return;
                }
                $point = \Cache::get('nominatim_search_' . auth()->user()->id)[(int) $state];
                $this->setLocation($point, $set, $livewire);
            });

        $components[] = Placeholder::make('address_was_changed')
            ->label(__('map_points.new_address'))
            ->hidden(fn (Get $get) => empty($get('new_address')))
            ->content(fn (Get $get) => $get('new_address'));

        $components[] = Map::make('location')
            ->live()
            ->hiddenLabel()
            ->liveLocation()
            ->zoom(18)
            ->draggable(false)
            ->showMyLocationButton()
            ->hintAction(
                Action::make('change_pin_location')
                    ->icon('heroicon-m-map-pin')
                    ->requiresConfirmation()
                    ->hidden(fn (Get $get) => empty($get('location.lat')))
                    ->form(function (Get $get) {
                        return[
                            Map::make('new_point')
                                ->defaultLocation($get('location.lat'), $get('location.lng'))
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
                                    \Cache::forget('nominatim_reverse_' . auth()->user()->id);
                                    $newAddress = Nominatim::make()->reverse($get('new_lat'), $get('new_lng'));
                                    \Cache::remember('nominatim_reverse_' . auth()->user()->id, 60, function () use ($newAddress) {
                                        return $newAddress;
                                    });

                                    return $newAddress->name;
                                }),
                            Toggle::make('change_address')
                                ->hidden(fn (Get $get) => empty($get('new_lat')))
                                ->label(__('map_points.change_address')),

                        ];
                    })
                    ->label(__('map_points.change_pin_location'))
                    ->action(function (array $data, $livewire, Set $set) {
                        if ($data['change_address']) {
                            $newAddress = \Cache::get('nominatim_reverse_' . auth()->user()->id);
                            \Cache::forget('nominatim_reverse_' . auth()->user()->id);
                            $this->setLocation($newAddress, $set, $livewire);
                            $set('new_address', $newAddress->name);
                            $set('nominatim_autocomplete', null);
                        } else {
                            $set('location', $data['new_point']);
                            $livewire->dispatch('refreshMap');
                        }
                    })
            )
            ->columnSpanFull();

        $components[] = Hidden::make('county_id');
        $components[] = Hidden::make('city_id');
        $components[] = Hidden::make('address');

        return [
            Forms\Components\Grid::make(1)
                ->schema(
                    $components
                )->hiddenLabel(),
        ];
    }

    protected function setLocation(Location $point, Set $set, \Livewire\Component $livewire): void
    {
        $city = City::search((string) $point->city)
            ->where('county', (string) $point->county)
            ->first();
        $set('location.lat', $point->center[0]);
        $set('location.lng', $point->center[1]);
        $set('county_id', $city->county_id);
        $set('city_id', $city->id);
        $set('address', $point->name);
        $livewire->dispatch('refreshMap');
    }

    public function getAutocompleteSearchDebounce(): ?int
    {
        return $this->evaluate($this->autocompleteSearchDebounce);
    }
}
