<?php

namespace App\Filament\Resources\MapPointsResource\Pages;

use App\Filament\Resources\MapPointsResource;
use App\Models\ActionLog as ActionLogModel;
use App\Models\MapPointGroup as MapPointGroupModel;
use App\Models\MapPointType as MapPointTypeModel;
use App\Models\RecycleMaterial as RecycleMaterialModel;
use App\Models\User as UserModel;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ViewField;
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

    protected static string $resource = MapPointsResource::class;

    protected static string $view = 'filament.resources.puncte-harta-resource.pages.view-punct-harta';

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

    public function editLocationAction(): Action
    {
        return Action::make('editLocation')
            ->icon('heroicon-m-pencil-square')
            ->iconButton()
            ->form([
                Section::make('Location')
                    ->description('Update map point location')
                    ->schema([
                        TextInput::make('county')
							->required()
							->default($this->getRecord()->county)->disabled(),
                        TextInput::make('city')
							->required()
							->default($this->getRecord()->city)->disabled(),
                        TextInput::make('address')
							->required()
							->default($this->getRecord()->address),
                        // TextInput::make('lat')->required()->default($this->getRecord()->lat),
                        // TextInput::make('lon')->required()->default($this->getRecord()->lon),
                        TextInput::make('location_notes')
							->required()
							->default($this->getRecord()->location_notes),

                    ])
                    ->columns(2),
                ViewField::make('map')->view('filament.forms.components.map'),

            ])
            ->action(function ($data)
            {
                $data['lat'] = $this->lat;
                $data['lon'] = $this->lon;
                $data['city'] = $this->city;
                $this->getRecord()->updateAddress(collect($data));
            });
    }

    public function editDetailsAction(): Action
    {
        return Action::make('editDetails')
            ->icon('heroicon-m-pencil-square')
            ->iconButton()
            ->form([
                Select::make('type')
					->label(__('map_points.point_type_alt'))
                    ->options(MapPointTypeModel::query()->pluck('display_name', 'id'))
                    ->default($this->getRecord()->type->id)
                    ->required(),
                Select::make('materials')
					->label(__('map_points.materials'))
                    ->options(RecycleMaterialModel::query()->pluck('name', 'id'))
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
							->seconds('false')
							->hoursStep(1)
                            ->minutesStep(10),
                        TimePicker::make('end_hour')
							->seconds('false')
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
            ->action(function ($data)
            {
                $this->getRecord()->updateDetails(collect($data));
            });
    }

    protected function getHeaderActions(): array
    {
        $actions = [];
        if (auth()->user()->can('manage_map_points'))
        {
            $actions = array_merge($actions, [
                Action::make('validate-point')
					->label(__('map_points.buttons.change_status'))
					->icon('heroicon-m-check')
                    ->action(function (array $data, Collection $records): void
                    {
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
                            ->options(MapPointGroupModel::query()->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(function (array $data, Collection $records): void
                    {
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
                    ->action(function ()
                    {
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
        return $this->getRecord()->type->display_name . ' ' . $this->getRecord()->managed_by;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ActionLogModel::whereModel(\get_class($this->getRecord()))->whereModelId($this->getRecord()->id)->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('user_id')
					->formatStateUsing(function (string $state, $record)
					{
						if ($record->user_id > 0)
						{
							$user = UserModel::find($record->user_id);
						}
						else
						{
							$user = new UserModel();
							$user->name = 'System';
						}
	
						return "[{$record->user_id}] {$user->name}";
					})
					->html(),
                TextColumn::make('action')
					->formatStateUsing(function (string $state, $record)
					{
						return trans('actions.' . $record->action);
					})
					->html(),
                TextColumn::make('old_values')
					->formatStateUsing(function (string $state, $record)
					{
						return ActionLogModel::formatValuesText($record, 'old_values');
					})
					->wrap()
					->html(),
                TextColumn::make('new_values')
					->formatStateUsing(function (string $state, $record)
					{
						return ActionLogModel::formatValuesText($record, 'new_values');
					})
					->wrap()
					->html(),
                TextColumn::make('created_at'),

            ]);
    }
}
