<?php

declare(strict_types=1);

namespace App\Filament\Resources\MapPointsResource\Pages;

use App\Filament\Resources\PointResource;
use App\Models\ActionLog as ActionLogModel;
use App\Models\Material;
use App\Models\PointGroup;
use App\Models\ServiceType;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class ViewMapPoint extends ViewRecord implements HasTable, HasActions
{
    use InteractsWithTable;

    protected static string $resource = PointResource::class;

//    protected static string $view = 'filament.resources.puncte-harta-resource.pages.view-punct-harta';

    public $lat;

    public $lon;

    public $city;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function getPostFormSchema(): array
    {
        return [
            TextInput::make('title')->required(),
        ];
    }

    public function editDetailsAction(): Action
    {
        return Action::make('editDetails')
            ->icon('heroicon-m-pencil-square')
            ->iconButton()
            ->form([
                Select::make('type')
                    ->label(__('map_points.point_type_alt'))
                    ->options(ServiceType::query()->pluck('name', 'id'))
                    ->default($this->getRecord()->service_type_id)
                    ->required(),
                Select::make('materials')
                    ->label(__('map_points.materials'))
                    ->options(Material::query()->pluck('name', 'id'))
                    ->default($this->getRecord()->materials->pluck('id')->toArray())
                    ->multiple()
                    ->required(),
                TextInput::make('managed_by')
                    ->required()
                    ->default($this->getRecord()->managed_by),
                TextInput::make('website')
                    ->required()
                    ->default($this->getRecord()->website),
                TextInput::make('email')
                    ->required()
                    ->default($this->getRecord()->email)->email(),
                TextInput::make('phone_no')
                    ->required()
                    ->default($this->getRecord()->phone_no),
                Repeater::make('opening_hours')
                    ->schema([
                        Select::make('start_day')
                            ->label('Start day')
                            ->options(__('common.week_days'))
                            ->required(),
                        Select::make('end_day')
                            ->label('End day')
                            ->options(__('common.week_days'))
                            // ->default($this->getRecord()->materials->pluck('id')->toArray())
                            ->required(),
                        TimePicker::make('start_hour')
                            ->seconds(false)
                            ->hoursStep(1)
                            ->minutesStep(10),
                        TimePicker::make('end_hour')
                            ->seconds(false)
                            ->hoursStep(1)
                            ->minutesStep(10),
                    ])
                    ->default($this->getRecord()->opening_hours)
                    ->columns(4),
                TextInput::make('notes')
                    ->required()
                    ->default($this->getRecord()->notes),
                Checkbox::make('offers_transport')->default($this->getRecord()->offers_transport),
                Checkbox::make('offers_money')->default($this->getRecord()->offers_money),
            ])
            ->action(function ($data) {
                $this->getRecord()->updateDetails(collect($data));
            });
    }

    protected function getHeaderActions(): array
    {
        $actions = [];
        if (auth()->user()->can('manage_map_points')) {
            $actions = array_merge($actions, [
                Action::make('validate-point')
                    ->label(__('map_points.buttons.change_status'))
                    ->icon('heroicon-m-arrows-right-left')
                    ->action(function (array $data, Collection $records): void {
                        $this->record->changeStatus();

                        Notification::make()
                            ->title(__('map_points.point_save_success'))
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                Action::make('updateGroup')
                    ->label(__('map_points.buttons.set_group'))
                    ->form([
                        Select::make('group_id')
                            ->label(__('map_points.buttons.group'))
                            ->options(PointGroup::query()->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(function (array $data, Collection $records): void {
                        $this->record->changeGroup($data['group_id']);

                        Notification::make()
                            ->title(__('map_points.point_save_success'))
                            ->success()
                            ->send();
                    })
                    ->icon('heroicon-o-user-group')
                    ->color('info'),
                Action::make('delete')
                    ->requiresConfirmation()
                    ->action(function () {
                        $this->getRecord()->delete();

                        return redirect($this->getResource()::getUrl('index'));
                    })
                    ->color('danger'),
            ]);
        }

        return $actions;
    }

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament-panels::resources/pages/view-record.breadcrumb');
    }

    public function getContentTabLabel(): ?string
    {
        return __('filament-panels::resources/pages/view-record.content.tab.label');
    }

    public function getTitle(): string | Htmlable
    {
        $record = $this->getRecord();

        $title = '#' . $this->getRecord()->id;

        return new HtmlString($title);

        //
    }

    public function getSubHeading(): string | Htmlable
    {
        return $this->getRecord()->serviceType->name . ' ' . $this->getRecord()->managed_by;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(
            [
                Section::make(__('map_points.sections.location'))
                    ->schema(
                        [
                            TextEntry::make('address')
                                ->icon('heroicon-s-map-pin')
                                ->label(__('map_points.fields.address')),

                            TextEntry::make('location')
                                ->icon('heroicon-s-ellipsis-horizontal-circle')
                                ->label(__('map_points.fields.coordinate')),

                            TextEntry::make('notes')
                                ->icon('heroicon-s-pencil-square')
                                ->label(__('map_points.fields.notes')),
                        ]
                    )->headerActions(
                        [
                            \Filament\Infolists\Components\Actions\Action::make('updateLocation')
                                ->hiddenLabel()
                                ->icon('heroicon-s-pencil-square')
                                ->form(
                                    [
                                        TextInput::make('lat')
                                            ->label('Lat')
                                            ->required(),
                                        TextInput::make('lon')
                                            ->label('Lon')
                                            ->required(),
                                        TextInput::make('city')
                                            ->label('City')
                                            ->required(),
                                    ]
                                ),
                        ]
                    )->columnSpan(3),
                ViewEntry::make('map_location')
                    ->columnSpan(9)
                    ->view('filament.infolist.map'),

            ]
        )->columns(12);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ActionLogModel::whereModel(\get_class($this->getRecord()))->whereModelId($this->getRecord()->id)->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('user.name')
                    ->formatStateUsing(function (string $state, $record) {
                    })
                    ->html(),
                TextColumn::make('action')
                    ->formatStateUsing(function (string $state, $record) {
                        return trans('actions.' . $record->action);
                    })
                    ->html(),
                TextColumn::make('old_values')
                    ->formatStateUsing(function (string $state, $record) {
                        return ActionLogModel::formatValuesText($record, 'old_values');
                    })
                    ->wrap()
                    ->html(),
                TextColumn::make('new_values')
                    ->formatStateUsing(function (string $state, $record) {
                        return ActionLogModel::formatValuesText($record, 'new_values');
                    })
                    ->wrap()
                    ->html(),
                TextColumn::make('created_at'),

            ]);
    }
}
