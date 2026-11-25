<?php

declare(strict_types=1);

namespace App\Livewire\Issues;

use App\Models\IssueOld;
use App\Models\IssueType as IssueTypeModel;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class IssueType2 extends Component implements HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $data = [];

    public IssueOld $record;

    public function mount(): void
    {
        $this->form->fill($this->record->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Radio::make('reported_point_issue_type_id')
                    ->options(IssueTypeModel::all()->pluck('title', 'id')->toArray())
                    ->label(__('issues.labels.issue_type'))
                    ->disabled(),
                Textarea::make('description')
                    ->label(__('issues.labels.address_is_not_correct'))
                    ->readonly(),
                Textarea::make('map_point')
                    ->label(__('issues.labels.original_address'))
                    ->formatStateUsing(function ($state, $record) {
                        return $record->map_point->address;
                    })
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
