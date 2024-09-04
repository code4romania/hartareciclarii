<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use App\Models\County;
use App\Services\Nominatim;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns;
use Filament\Forms\Set;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class LeafletAutocomplete extends Component
{
    use Concerns\HasName;

    protected string $view = 'filament-forms::components.fieldset';

    protected bool|Closure $isRequired = false;

    protected array $params = [];

    public ?array $withFields = [];

    public array $location = [];

    protected string|Closure $autocompleteFieldColumnSpan = 'full';

    protected int|Closure $autocompleteSearchDebounce = 2000; // 2 seconds

    protected array|Closure $addressFieldsColumns = [];

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

        $components[] = Forms\Components\Select::make('google_autocomplete')
            ->native(false)
            ->dehydrated(false)
            ->allowHtml()
            ->live()
            ->searchDebounce($this->getAutocompleteSearchDebounce()) // 2 seconds
            ->searchingMessage(__(''))
            ->searchPrompt(__('Type to search...'))
            ->searchable()
            ->hint(new HtmlString(Blade::render('<x-filament::loading-indicator class="h5 w-5" wire:loading wire:target="data.google_autocomplete" />')))
            ->columnSpan($this->getAutocompleteFieldColumnSpan())
            ->getSearchResultsUsing(function (string $search): array {
                $result = Nominatim::make()->search($search);
                return $result->pluck('name', 'id')
                    ->toArray();
            })
            ->afterStateUpdated(function (?string $state, Set $set) {

                $place = Nominatim::make()
                    ->getPlaceById((int)$state);

                debug($place);
                $addressObject = collect(Arr::get($place, 'address'));
                $city = $addressObject->get('city') ?? $addressObject->get('village');
                $county = $addressObject->get('county');
                $countyModel = County::where('name', $county)->with('cities')->first();
                $cityModel = $countyModel->cities->firstWhere('name', $city);
                $set('county_id', $countyModel->id);
                $set('city_id', $cityModel->id);
                $set('address', $place['display_name']);
                $set('lat', $place['lat']);
                $set('long', $place['lon']);
            });

        $components[] = Forms\Components\ViewField::make('map')
            ->columnSpanFull()
            ->view('filament.forms.components.leaflet-autocomplete');

        $components[] = Forms\Components\Hidden::make('county_id');
        $components[] = Forms\Components\Hidden::make('city_id');
        $components[] = Forms\Components\Hidden::make('address');
        $components[] = Forms\Components\Hidden::make('lat');
        $components[] = Forms\Components\Hidden::make('long');

        return [
            Forms\Components\Grid::make($this->getAddressFieldsColumns())
                ->schema(
                    $components
                ),
        ];
    }

    protected function replaceFieldPlaceholders($googleField, $googleFields, $googleFieldValue)
    {
        preg_match_all('/{(.*?)}/', $googleField, $matches);

        foreach ($matches[1] as $item) {
            $valueToReplace = isset($googleFields[$item][$googleFieldValue]) ? $googleFields[$item][$googleFieldValue] : '';

            $googleField = str_ireplace('{' . $item . '}', $valueToReplace, $googleField);
        }

        return $googleField;
    }

    public function withFields(null|array|string|Closure $fields): static
    {
        $this->withFields = $fields;

        return $this;
    }

    public function getWithFields(): ?array
    {
        if (empty($this->withFields)) {
            return [
                Forms\Components\TextInput::make('address')
                    ->extraInputAttributes([
                        'data-google-field' => '{street_number} {route}, {sublocality_level_1}',
                    ]),
                Forms\Components\TextInput::make('city')
                    ->extraInputAttributes([
                        'data-google-field' => 'locality',
                    ]),
                Forms\Components\TextInput::make('country'),
                Forms\Components\TextInput::make('zip')
                    ->extraInputAttributes([
                        'data-google-field' => 'postal_code',
                    ]),
            ];
        }

        return $this->evaluate($this->withFields);
    }

    public function autocompleteFieldColumnSpan(string|Closure $autocompleteFieldColumnSpan = 'full'): static
    {
        $this->autocompleteFieldColumnSpan = $autocompleteFieldColumnSpan;

        $this->params['autocompleteFieldColumnSpan'] = $autocompleteFieldColumnSpan;

        return $this;
    }

    public function getAutocompleteFieldColumnSpan(): ?string
    {
        return $this->evaluate($this->autocompleteFieldColumnSpan);
    }

    public function addressFieldsColumns(null|array|string|Closure $addressFieldsColumns): static
    {
        $this->addressFieldsColumns = $addressFieldsColumns;

        return $this;
    }

    public function getAddressFieldsColumns(): ?array
    {
        if (empty($this->addressFieldsColumns)) {
            return [
                'default' => 1,
                'sm' => 2,
            ];
        }

        return $this->evaluate($this->addressFieldsColumns);
    }

    public function autocompleteSearchDebounce(int|Closure $autocompleteSearchDebounce = 2000): static
    {
        $this->autocompleteSearchDebounce = $autocompleteSearchDebounce;

        $this->params['autocompleteSearchDebounce'] = $autocompleteSearchDebounce;

        return $this;
    }

    public function getAutocompleteSearchDebounce(): ?int
    {
        return $this->evaluate($this->autocompleteSearchDebounce);
    }
}
