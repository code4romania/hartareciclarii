<?php

declare(strict_types=1);

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\IssueType as IssueTypeModel;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class IssueType4 extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public Issue $record;

    public function mount(): void
    {
        $this->form->fill($this->record->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('reported_point_issue_type_id')
                    ->options(IssueTypeModel::all()->pluck('title', 'id')->toArray())
                    ->label(__('issues.labels.issue_type'))
                    ->disabled(),
                Textarea::make('description')
                    ->label(__('issues.labels.address_is_not_correct'))
                    ->readonly(),
                Repeater::make('issues.labels.opening_hours')
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
                    ->default($this->record->map_point->opening_hours)
                    ->columns(4)
                    ->disabled(),
            ])

            ->statePath('data')
            ->model($this->record);
    }

    public function render(): View
    {
        return view('filament.resources.issues-resource.pages.view');
    }
}
