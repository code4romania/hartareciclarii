<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointResource\Actions\Page;

use App\Enums\Point\Source;
use App\Filament\Forms\Components\LeafletAutocomplete;
use App\Models\MaterialCategory;
use App\Models\Point;
use App\Models\PointType;
use App\Models\ServiceType;
use Filament\Actions\Action;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use MatanYadaev\EloquentSpatial\Objects\Point as PointObject;

class AddPoint extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'add_point';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->icon('heroicon-m-plus');
        $this->label(__('map_points.buttons.create'));
        $this->modal();
        $this->steps([
            Step::make(__('map_points.wizard.step_1'))->schema(
                [
                    Select::make('service_type_id')
                        ->label(__('map_points.fields.service_type'))
                        ->live()
                        ->options(
                            ServiceType::all()->pluck('name', 'id')
                                ->toArray()
                        )
                        ->afterStateUpdated(fn (string $state, Set $set) => $set('chosenService', ServiceType::find($state)))
                        ->required(),
                    LeafletAutocomplete::make('location')->hiddenLabel(),
                ]
            ),
            Step::make(__('map_points.wizard.step_2'))->schema(
                [
                    Select::make('point_type_id')
                        ->label(__('map_points.fields.point_type'))
                        ->options(
                            fn (Get $get) => PointType::where('service_type_id', $get('service_type_id'))
                                ->get()
                                ->pluck('name', 'id')
                        )
                        ->required(),

                    TextInput::make('business_name')
                        ->label(__('map_points.fields.business_name'))
                        ->hidden(fn (Get $get) => $this->showField($get, 'can_have_business_name')),

                    TextInput::make('administered_by')
                        ->label(__('map_points.fields.administered_by'))
                        ->disabled(fn (Get $get) => $get('unknown_administered_by'))
                        ->required(fn (Get $get) => $get('unknown_administered_by') === false),
                    Checkbox::make('unknown_administered_by')
                        ->live()
                        ->label(__('add_point.details.unknown_program'))
                        ->default(false),

                    TextInput::make('schedule')
                        ->label(__('map_points.fields.schedule'))
                        ->disabled(fn (Get $get) => $get('unknown_schedule'))
                        ->required(fn (Get $get) => $get('unknown_schedule') === false),
                    Checkbox::make('unknown_schedule')
                        ->live()
                        ->label(__('add_point.details.unknown_program'))
                        ->default(false),

                    Select::make('offers_money')
                        ->label(__('map_points.fields.offers_money'))
                        ->hidden(fn (Get $get) => $this->showField($get, 'can_offer_money'))
                        ->boolean(),

                    Select::make('offers_transport')
                        ->label(__('map_points.fields.offers_transport'))
                        ->hidden(fn (Get $get) => $this->showField($get, 'can_offer_transport'))
                        ->boolean(),

                    Select::make('offers_vouchers')
                        ->label(__('map_points.fields.offers_vouchers'))
                        ->hidden(fn (Get $get) => $this->showField($get, 'can_offer_vouchers'))
                        ->boolean(),

                    Select::make('free_of_charge')
                        ->label(__('map_points.fields.free_of_charge'))
                        ->hidden(fn (Get $get) => $this->showField($get, 'can_request_payment'))
                        ->boolean(),

                    TextInput::make('website')
                        ->url()
                        ->label(__('map_points.fields.website'))
                        ->hidden(fn (Get $get) => $this->showField($get, 'can_have_business_name')),

                    TextInput::make('email')
                        ->label(__('map_points.fields.email'))
                        ->email()
                        ->hidden(fn (Get $get) => $this->showField($get, 'can_have_business_name')),

                    TextInput::make('phone')
                        ->label(__('map_points.fields.phone'))
                        ->hidden(fn (Get $get) => $this->showField($get, 'can_have_business_name')),

                    Textarea::make('observations')
                        ->label(__('map_points.fields.observations')),

                ]
            ),

            Step::make(__('map_points.wizard.step_3'))
                ->columns(2)
                ->statePath('materials')
                ->schema([
                    ...$this->getMaterialsFields(),
                ])->hidden(fn (Get $get) => empty($get('chosenService')) || $get('chosenService.can_collect_materials') === false),

        ]);
        $this->modalSubmitAction(fn (StaticAction $action) => $action->label(__('map_points.submit')));
        $this->action(function (array $data) {
            $point = Point::create([
                'source' => Source::MANUAL_BY_ADMIN,
                'created_by' => auth()->id(),
                'service_type_id' => $data['service_type_id'],
                'point_type_id' => $data['point_type_id'],
                'location' => new PointObject($data['location']['lat'], $data['location']['lng']),
                'county_id' => $data['county_id'],
                'city_id' => $data['city_id'],
                'address' => $data['address'],
                'business_name' => $data['business_name'] ?? null,
                'administered_by' => $data['administered_by'] ?? null,
                'schedule' => $data['schedule'] ?? null,
                'offers_money' => $data['offers_money'] ?? null,
                'offers_transport' => $data['offers_transport'] ?? null,
                'offers_vouchers' => $data['offers_vouchers'] ?? null,
                'free_of_charge' => $data['free_of_charge'] ?? null,
                'website' => $data['website'] ?? null,
                'verified_at' => now(),
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]);
            if (isset($data['materials'])) {
                $materialsToAttach = [];
                collect($data['materials'])
                    ->values()
                    ->map(function ($materials) use (&$materialsToAttach) {
                        foreach ($materials as $key => $value) {
                            if ($value) {
                                $materialsToAttach[] = $key;
                            }
                        }
                    });
                $point->materials()->sync($materialsToAttach);
            }
            Notification::make(__('map_points.notifications.point_added'))
                ->body(__('map_points.notifications.point_added_body', ['point' => $point->id]))
                ->success()
                ->send();
        });
    }

    private function showField(Get $get, string $string): bool
    {
        if ($get('chosenService') === null) {
            return false;
        }

        return ! $get('chosenService.' . $string);
    }

    public static function getMaterialsFields(): array
    {
        $categories = MaterialCategory::with('materials')->orderBy('position')->get();
        $fields = [];

        foreach ($categories as $category) {
            $fields[] = Section::make($category->name)
                ->schema(
                    $category->materials->map(function ($material) {
                        return Checkbox::make("$material->id")
                            ->label($material->name);
                    })->toArray()
                )->collapsed(true)
                ->columnSpan(1)
                ->compact()
                ->statePath('category_' . $category->id)
                ->headerActions([
                    FormAction::make('select_all')
                        ->icon('heroicon-s-check')
                        ->hiddenLabel()
                        ->action(function (array $data, Set $set, Get $get) use ($category) {
                            $materials = $get('category_' . $category->id);
                            foreach ($materials as $material => $value) {
                                $set('category_' . $category->id . '.' . $material, true);
                            }
                        }),
                    FormAction::make('deselct_all')
                        ->icon('heroicon-s-x-circle')
                        ->hiddenLabel()
                        ->color('danger')
                        ->action(function (array $data, Set $set, Get $get) use ($category) {
                            $materials = $get('category_' . $category->id);
                            foreach ($materials as $material => $value) {
                                $set('category_' . $category->id . '.' . $material, false);
                            }
                        }),
                ]);
        }

        return $fields;
    }
}
