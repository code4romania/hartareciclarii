<?php

declare(strict_types=1);

/**
 * @Author: Bogdan Bocioaca
 * @Date:   2023-11-27 07:54:52
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-11-27 07:59:03
 */

namespace App\Livewire\Issues;

use App\Models\IssueOld;
use App\Models\IssueType as IssueTypeModel;
use App\Models\IssueTypeItem as IssueTypeItemModel;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class IssueType6 extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public IssueOld $record;

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
                Radio::make('collection_decline_reason')
                    ->options(IssueTypeModel::all()->pluck('title', 'id')->toArray())
                    ->label(__('issues.labels.decline_reason'))
                    ->options(IssueTypeItemModel::where('reported_point_issue_type_id', 6)->get()->pluck('title', 'id')->toArray())
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
