<?php

declare(strict_types=1);

/**
 * @Author: Bogdan Bocioaca
 * @Date:   2023-11-09 20:15:28
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-11-09 20:38:41
 */

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\IssueType as IssueTypeModel;
use App\Models\IssueTypeItem as IssueTypeItemModel;
use App\Models\RecycleMaterial as RecycleMaterialModel;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get as Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class IssueType3 extends Component implements HasForms
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
                Radio::make('material_issue')
                    ->options(IssueTypeItemModel::where('reported_point_issue_type_id', $this->record->reported_point_issue_type_id)->get()->pluck('title', 'id')->toArray())
                    ->label(__('issues.labels.issue_type'))
                    ->disabled(),

                CheckboxList::make('material_issue_missing')
                    ->label(new HtmlString(__('issues.labels.material_issue_missing')))
                    ->options(RecycleMaterialModel::all()->pluck('name', 'id')->toArray())
                    ->disabled()
                    ->hidden(
                        fn (Closure $get): bool => \count($get('material_issue_missing')) == 0
                    )->columns(4),
                CheckboxList::make('material_issue_extra')
                    ->label(new HtmlString(__('issues.labels.material_issue_extra')))
                    ->options(RecycleMaterialModel::all()->pluck('name', 'id')->toArray())
                    ->disabled()
                    ->hidden(
                        fn (Closure $get): bool => \count($get('material_issue_extra')) == 0
                    )->columns(4),

            ])

            ->statePath('data')
            ->model($this->record);
    }

    public function render(): View
    {
        return view('filament.resources.issues-resource.pages.view');
    }
}
