<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\IssueType as IssueTypeModel;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class IssueType7 extends Component implements HasForms
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
                    ->label(__('issues.labels.description'))
                    ->readonly(),
            ])

            ->statePath('data')
            ->model($this->record);
    }

    public function render(): View
    {
        return view('filament.resources.issues-resource.pages.view');
    }
}
