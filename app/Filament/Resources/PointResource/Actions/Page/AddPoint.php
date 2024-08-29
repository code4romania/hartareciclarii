<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointResource\Actions\Page;

use App\Filament\Forms\Components\LeafletAutocomplete;
use App\Models\ServiceType;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;

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
        $this->form([
            Wizard::make([
                Step::make(__('map_points.wizard.step_1'))->schema([
                        Select::make('service_type_id')
                            ->label(__('map_points.fields.service_type'))
                            ->options(
                                ServiceType::all()->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->required(),
                        LeafletAutocomplete::make('location')
                            ->label(__('map_points.fields.location')),
                    ]
                ),
                Step::make(__('map_points.wizard.step_2'))->schema(
                    [
                        Select::make('service_type_id')
                            ->label(__('map_points.fields.service_type'))
                            ->options(
                                ServiceType::all()->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->required(),
                    ]
                ),

            ]),


        ]);
    }
}
